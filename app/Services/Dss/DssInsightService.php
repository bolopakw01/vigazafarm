<?php

namespace App\Services\Dss;

use App\Models\Kematian;
use App\Models\MonitoringLingkungan;
use App\Models\Pakan;
use App\Models\ParameterStandar;
use App\Models\Pembesaran;
use App\Models\StokPakan;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DssInsightService
{
    protected Carbon $today;

    public function __construct(?Carbon $referenceDate = null)
    {
        $this->today = $referenceDate?->copy() ?? now();
    }

    public function getInsights(): array
    {
        return [
            'feed' => $this->buildFeedInsights(),
            'stock' => $this->buildStockAlerts(),
            'environment' => $this->buildEnvironmentAlerts(),
            'health' => $this->buildHealthAlerts(),
        ];
    }

    protected function buildFeedInsights(): array
    {
        $limit = (int) config('dss.feed.max_insights', 5);

        $batches = Pembesaran::query()
            ->with('kandang')
            ->where(function ($query) {
                $query->whereNull('status_batch')
                    ->orWhereNotIn('status_batch', ['selesai', 'closed', 'selesai_transfer']);
            })
            ->orderByDesc('tanggal_masuk')
            ->take($limit * 2)
            ->get()
            ->filter(fn ($batch) => !empty($batch->batch_produksi_id))
            ->values()
            ->take($limit);

        if ($batches->isEmpty()) {
            return [];
        }

        $batchIds = $batches->pluck('batch_produksi_id')->filter()->values();
        $historyDays = max(1, (int) config('dss.feed.history_days', 7));
        $historyStart = $this->today->copy()->subDays($historyDays - 1)->startOfDay();

        $feedHistoryTotals = Pakan::query()
            ->whereIn('batch_produksi_id', $batchIds)
            ->whereBetween('tanggal', [$historyStart->toDateString(), $this->today->toDateString()])
            ->selectRaw('batch_produksi_id, COALESCE(SUM(jumlah_kg), 0) as total')
            ->groupBy('batch_produksi_id')
            ->pluck('total', 'batch_produksi_id');

        $feedTodayTotals = Pakan::query()
            ->whereIn('batch_produksi_id', $batchIds)
            ->whereDate('tanggal', $this->today->toDateString())
            ->selectRaw('batch_produksi_id, COALESCE(SUM(jumlah_kg), 0) as total')
            ->groupBy('batch_produksi_id')
            ->pluck('total', 'batch_produksi_id');

        $totalDeaths = Kematian::query()
            ->whereIn('batch_produksi_id', $batchIds)
            ->selectRaw('batch_produksi_id, COALESCE(SUM(jumlah), 0) as total')
            ->groupBy('batch_produksi_id')
            ->pluck('total', 'batch_produksi_id');

        return $batches->map(function (Pembesaran $batch) use ($feedHistoryTotals, $feedTodayTotals, $totalDeaths, $historyDays) {
            $batchId = $batch->batch_produksi_id;
            $umurHari = $this->resolveUmurHari($batch);
            $phase = $this->determinePhase($umurHari);
            $phaseLabel = Arr::get($phase, 'label', 'Growth');
            $targetPerBird = (float) Arr::get($phase, 'target_feed_per_bird_kg', 0.02);

            $populasiAwal = $this->resolvePopulasiAwal($batch);
            $populasiSaatIni = max(0, $populasiAwal - (int) ($totalDeaths[$batchId] ?? 0));
            if ($populasiSaatIni === 0 && $populasiAwal > 0) {
                $populasiSaatIni = $populasiAwal;
            }

            $targetTotal = round($populasiSaatIni * $targetPerBird, 2);
            $actualToday = round((float) ($feedTodayTotals[$batchId] ?? 0), 2);
            $avgPerDay = $historyDays > 0
                ? round((float) ($feedHistoryTotals[$batchId] ?? 0) / $historyDays, 2)
                : 0.0;
            $delta = round($actualToday - $targetTotal, 2);

            $status = $this->evaluateFeedStatus($targetTotal, $delta);

            return [
                'batch' => $batchId,
                'kandang' => optional($batch->kandang)->nama_kandang ?? 'Kandang ' . ($batch->kandang_id ?? '?'),
                'fase' => $phaseLabel,
                'umur_hari' => $umurHari,
                'populasi' => $populasiSaatIni,
                'target_kg' => $targetTotal,
                'actual_kg' => $actualToday,
                'avg7day_kg' => $avgPerDay,
                'delta_kg' => $delta,
                'status' => $status,
            ];
        })->values()->all();
    }

    protected function buildStockAlerts(): array
    {
        $limit = (int) config('dss.stock.max_items', 4);
        $stocks = StokPakan::query()
            ->orderBy('stok_kg')
            ->take($limit * 2)
            ->get();

        if ($stocks->isEmpty()) {
            return [];
        }

        $stockIds = $stocks->pluck('id')->filter()->values();
        $usageAverages = $this->fetchStockUsageAverages($stockIds);

        $warningCover = (float) config('dss.stock.cover_warning_days', 5);
        $criticalCover = (float) config('dss.stock.cover_critical_days', 2);

        return $stocks->map(function (StokPakan $stock) use ($usageAverages, $warningCover, $criticalCover) {
            $avgUsage = $usageAverages[$stock->id] ?? null;
            $coverDays = ($avgUsage && $avgUsage > 0)
                ? round($stock->stok_kg / $avgUsage, 1)
                : null;

            $level = 'ok';
            $message = 'Stok aman';

            if ($stock->isExpired()) {
                $level = 'critical';
                $message = 'Stok kadaluarsa';
            } elseif ($stock->isNearExpiry()) {
                $level = 'warning';
                $message = 'Stok mendekati kadaluarsa';
            } elseif ($stock->isLowStock()) {
                $level = 'warning';
                $message = 'Stok pakan rendah';
            }

            if ($coverDays !== null) {
                if ($coverDays <= $criticalCover) {
                    $level = 'critical';
                    $message = 'Stok hanya cukup ' . $coverDays . ' hari';
                } elseif ($coverDays <= $warningCover && $level !== 'critical') {
                    $level = 'warning';
                    $message = 'Stok kurang dari ' . $warningCover . ' hari';
                }
            }

            return [
                'nama' => $stock->nama_pakan ?? $stock->kode_pakan ?? 'Pakan Tanpa Nama',
                'stok_kg' => (float) $stock->stok_kg,
                'stok_karung' => $stock->stok_karung,
                'cover_days' => $coverDays,
                'status' => $level,
                'message' => $message,
                'updated_at' => optional($stock->updated_at)->format('d/m/Y H:i'),
            ];
        })->values()
        ->take($limit)
        ->all();
    }

    protected function buildEnvironmentAlerts(): array
    {
        $limit = (int) config('dss.environment.max_items', 4);
        $records = MonitoringLingkungan::query()
            ->with('kandang')
            ->orderByDesc('waktu_pencatatan')
            ->limit($limit * 5)
            ->get();

        if ($records->isEmpty()) {
            return [];
        }

        $latestPerKandang = $records
            ->sortByDesc('waktu_pencatatan')
            ->unique('kandang_id')
            ->take($limit);

        $batchIds = $latestPerKandang->pluck('batch_produksi_id')->filter()->unique();
        $batchMap = $batchIds->isNotEmpty()
            ? Pembesaran::whereIn('batch_produksi_id', $batchIds)->with('kandang')->get()->keyBy('batch_produksi_id')
            : collect();

        return $latestPerKandang->map(function (MonitoringLingkungan $record) use ($batchMap) {
            $batch = $record->batch_produksi_id ? $batchMap->get($record->batch_produksi_id) : null;
            $umurHari = $batch ? $this->resolveUmurHari($batch) : null;
            $phase = $this->determinePhase($umurHari);
            $phaseKey = strtoupper(Arr::get($phase, 'key', 'grower'));
            $phaseLabel = Arr::get($phase, 'label', ucfirst(strtolower($phaseKey)));

            $temperature = $this->evaluateEnvironmentalParameter('suhu', (float) $record->suhu, $phaseKey);
            $humidity = $this->evaluateEnvironmentalParameter('kelembaban', (float) $record->kelembaban, $phaseKey);
            $overall = $this->mergeEnvironmentLevel($temperature, $humidity);

            return [
                'kandang' => optional($record->kandang)->nama_kandang ?? 'Kandang ' . ($record->kandang_id ?? '?'),
                'fase' => $phaseLabel,
                'suhu' => (float) $record->suhu,
                'kelembaban' => (float) $record->kelembaban,
                'ventilasi' => $record->kondisi_ventilasi,
                'waktu' => optional($record->waktu_pencatatan)->format('d/m/Y H:i'),
                'status' => $overall['level'],
                'message' => $overall['message'],
                'temperature' => $temperature,
                'humidity' => $humidity,
            ];
        })->values()->all();
    }

    protected function buildHealthAlerts(): array
    {
        $windowDays = max(1, (int) config('dss.health.window_days', 3));
        $limit = (int) config('dss.health.max_items', 4);
        $startDate = $this->today->copy()->subDays($windowDays - 1)->toDateString();

        $recentDeaths = Kematian::query()
            ->whereNotNull('batch_produksi_id')
            ->where('tanggal', '>=', $startDate)
            ->selectRaw('batch_produksi_id, COALESCE(SUM(jumlah), 0) as total')
            ->groupBy('batch_produksi_id')
            ->orderByDesc('total')
            ->take($limit * 3)
            ->get();

        if ($recentDeaths->isEmpty()) {
            return [];
        }

        $batchIds = $recentDeaths->pluck('batch_produksi_id')->filter()->unique();
        $batchMap = Pembesaran::whereIn('batch_produksi_id', $batchIds)
            ->with('kandang')
            ->get()
            ->keyBy('batch_produksi_id');

        $totalDeaths = Kematian::whereIn('batch_produksi_id', $batchIds)
            ->selectRaw('batch_produksi_id, COALESCE(SUM(jumlah), 0) as total')
            ->groupBy('batch_produksi_id')
            ->pluck('total', 'batch_produksi_id');

        $warningPct = (float) config('dss.health.warning_pct', 3);
        $criticalPct = (float) config('dss.health.critical_pct', 5);

        return $recentDeaths
            ->map(function ($row) use ($batchMap, $totalDeaths, $warningPct, $criticalPct, $windowDays) {
                $batch = $batchMap->get($row->batch_produksi_id);
                if (!$batch) {
                    return null;
                }

                $populasiAwal = $this->resolvePopulasiAwal($batch);
                $populasiSaatIni = max(0, $populasiAwal - (int) ($totalDeaths[$row->batch_produksi_id] ?? 0));
                if ($populasiSaatIni <= 0) {
                    return null;
                }

                $recentTotal = (int) $row->total;
                if ($recentTotal === 0) {
                    return null;
                }

                $mortalitasPct = round(($recentTotal / $populasiSaatIni) * 100, 2);
                $level = 'ok';

                if ($mortalitasPct >= $criticalPct) {
                    $level = 'critical';
                } elseif ($mortalitasPct >= $warningPct) {
                    $level = 'warning';
                }

                if ($level === 'ok') {
                    return null;
                }

                $recommendation = $level === 'critical'
                    ? 'Segera lakukan investigasi kesehatan dan cek biosecurity.'
                    : 'Perketat monitoring harian dan review SOP pemberian pakan/vitamin.';

                return [
                    'batch' => $row->batch_produksi_id,
                    'kandang' => optional($batch->kandang)->nama_kandang ?? 'Kandang ' . ($batch->kandang_id ?? '?'),
                    'populasi' => $populasiSaatIni,
                    'total_mati' => $recentTotal,
                    'mortalitas_pct' => $mortalitasPct,
                    'status' => $level,
                    'message' => 'Mortalitas ' . $mortalitasPct . '% dalam ' . $windowDays . ' hari',
                    'recommendation' => $recommendation,
                ];
            })
            ->filter()
            ->values()
            ->take($limit)
            ->all();
    }

    protected function resolveUmurHari(Pembesaran $batch): ?int
    {
        if (!empty($batch->umur_hari)) {
            return (int) $batch->umur_hari;
        }

        if ($batch->tanggal_masuk) {
            return Carbon::parse($batch->tanggal_masuk)->diffInDays($this->today) + 1;
        }

        return null;
    }

    protected function resolvePopulasiAwal(Pembesaran $batch): int
    {
        if (!empty($batch->jumlah_siap)) {
            return (int) $batch->jumlah_siap;
        }

        if (!empty($batch->jumlah_anak_ayam)) {
            return (int) $batch->jumlah_anak_ayam;
        }

        return 0;
    }

    protected function determinePhase(?int $umurHari): array
    {
        $phases = collect(config('dss.feed.phases', []));
        $umur = $umurHari ?? 0;

        $match = $phases->first(function ($phase) use ($umur) {
            $min = (int) ($phase['min_day'] ?? 0);
            $max = $phase['max_day'] ?? null;
            if ($umur < $min) {
                return false;
            }

            if ($max === null) {
                return true;
            }

            return $umur <= (int) $max;
        });

        return $match ?? $phases->last() ?? [];
    }

    protected function evaluateFeedStatus(float $targetKg, float $deltaKg): array
    {
        if ($targetKg <= 0 && abs($deltaKg) < 0.01) {
            return [
                'level' => 'info',
                'message' => 'Belum ada target konsumsi',
                'direction' => 'balanced',
                'delta_pct' => 0.0,
                'recommendation' => 'Pastikan populasi dan target pakan terdata.',
                'severity' => 0,
            ];
        }

        if ($targetKg <= 0) {
            return [
                'level' => 'info',
                'message' => 'Data target belum tersedia',
                'direction' => $deltaKg >= 0 ? 'over' : 'under',
                'delta_pct' => 0.0,
                'recommendation' => 'Lengkapi data populasi untuk menghitung target pakan.',
                'severity' => 0,
            ];
        }

        $ratio = abs($deltaKg) / $targetKg;
        $warning = (float) config('dss.feed.warning_ratio', 0.1);
        $critical = (float) config('dss.feed.critical_ratio', 0.2);
        $direction = $deltaKg >= 0 ? 'over' : 'under';

        if ($ratio >= $critical) {
            $level = 'critical';
        } elseif ($ratio >= $warning) {
            $level = 'warning';
        } else {
            $level = 'ok';
        }

        $percentage = round($ratio * 100, 1);
        $message = $percentage > 0
            ? $percentage . '% ' . ($direction === 'over' ? 'di atas' : 'di bawah') . ' target'
            : 'Konsumsi sesuai target';

        $recommendation = match ($level) {
            'critical' => $direction === 'under'
                ? 'Tingkatkan pemberian pakan atau cek kesehatan flock.'
                : 'Kurangi pakan untuk menghindari pemborosan.',
            'warning' => $direction === 'under'
                ? 'Pantau kembali jadwal pemberian pakan hari ini.'
                : 'Pastikan penimbangan ulang sebelum distribusi.',
            default => 'Pertahankan konsistensi jadwal pakan.',
        };

        return [
            'level' => $level,
            'message' => $message,
            'direction' => $direction,
            'delta_pct' => $percentage,
            'recommendation' => $recommendation,
            'severity' => match ($level) {
                'critical' => 2,
                'warning' => 1,
                default => 0,
            },
        ];
    }

    protected function fetchStockUsageAverages(Collection $stockIds): array
    {
        if ($stockIds->isEmpty()) {
            return [];
        }

        $windowDays = max(1, (int) config('dss.feed.history_days', 7));
        $startDate = $this->today->copy()->subDays($windowDays - 1)->toDateString();

        return Pakan::query()
            ->whereIn('stok_pakan_id', $stockIds)
            ->whereBetween('tanggal', [$startDate, $this->today->toDateString()])
            ->selectRaw('stok_pakan_id, COALESCE(SUM(jumlah_kg), 0) as total')
            ->groupBy('stok_pakan_id')
            ->pluck('total', 'stok_pakan_id')
            ->map(function ($total) use ($windowDays) {
                return $windowDays > 0 ? ((float) $total) / $windowDays : 0.0;
            })
            ->all();
    }

    protected function evaluateEnvironmentalParameter(string $parameter, float $value, string $phaseKey): array
    {
        $phaseKey = $this->normalizePhaseKey($phaseKey);
        $standard = ParameterStandar::byParameter($parameter, $phaseKey)->first();

        if ($standard) {
            $status = $standard->getStatus($value); // baik, perhatian, kritis
            $level = match ($status) {
                'kritis' => 'critical',
                'perhatian' => 'warning',
                'baik' => 'ok',
                default => 'unknown',
            };

            $min = $standard->nilai_minimal;
            $max = $standard->nilai_maksimal;

            $message = match (true) {
                $level === 'ok' => 'Dalam rentang standar',
                $value < $min => 'Di bawah batas minimal (' . $min . ')',
                $max !== null && $value > $max => 'Di atas batas maksimal (' . $max . ')',
                default => 'Perlu perhatian terhadap fluktuasi',
            };

            return [
                'parameter' => $parameter,
                'level' => $level,
                'status' => $status,
                'message' => $message,
                'range' => ['min' => $min, 'max' => $max],
            ];
        }

        return $this->buildFallbackRangeResponse($parameter, $value, $phaseKey);
    }

    protected function buildFallbackRangeResponse(string $parameter, float $value, string $phaseKey): array
    {
        $fallback = Arr::get(config('dss.environment.fallback_standards'), strtolower($phaseKey) . '.' . $parameter);

        if (!$fallback) {
            return [
                'parameter' => $parameter,
                'level' => 'unknown',
                'status' => 'unknown',
                'message' => 'Standar belum tersedia',
                'range' => null,
            ];
        }

        $min = $fallback['min'];
        $max = $fallback['max'];
        $level = ($value < $min || $value > $max) ? 'warning' : 'ok';
        $message = $level === 'ok'
            ? 'Dalam rentang referensi'
            : 'Di luar rentang referensi (' . $min . '-' . $max . ')';

        return [
            'parameter' => $parameter,
            'level' => $level,
            'status' => $level === 'ok' ? 'baik' : 'perhatian',
            'message' => $message,
            'range' => ['min' => $min, 'max' => $max],
        ];
    }

    protected function mergeEnvironmentLevel(array $temperature, array $humidity): array
    {
        $levels = [$temperature['level'] ?? 'unknown', $humidity['level'] ?? 'unknown'];

        if (in_array('critical', $levels, true)) {
            return [
                'level' => 'critical',
                'message' => 'Parameter lingkungan kritis, perlu tindakan segera.',
            ];
        }

        if (in_array('warning', $levels, true)) {
            return [
                'level' => 'warning',
                'message' => 'Ada parameter lingkungan yang perlu perhatian.',
            ];
        }

        if (in_array('ok', $levels, true)) {
            return [
                'level' => 'ok',
                'message' => 'Lingkungan stabil.',
            ];
        }

        return [
            'level' => 'info',
            'message' => 'Belum ada referensi standar.',
        ];
    }

    protected function normalizePhaseKey(string $phaseKey): string
    {
        $upper = strtoupper($phaseKey);
        if (in_array($upper, ['DOC', 'DOQ'], true)) {
            return 'DOQ';
        }

        return strtolower($phaseKey) === 'grower'
            ? 'grower'
            : (strtolower($phaseKey) === 'layer' ? 'layer' : $phaseKey);
    }
}
