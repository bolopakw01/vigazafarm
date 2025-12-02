<?php

namespace App\Services\Dss;

use App\Models\Kematian;
use App\Models\Pakan;
use App\Models\Pembesaran;
use App\Models\Penetasan;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class DssInsightService
{
    protected Carbon $today;

    public function __construct(?Carbon $referenceDate = null)
    {
        $this->today = $referenceDate ? $referenceDate->copy() : now();
    }

    public function getInsights(): array
    {
        return [
            'eggs' => $this->buildEggInsights(),
            'feed' => $this->buildFeedInsights(),
            'mortality' => $this->buildMortalityAlerts(),
        ];
    }

    protected function buildEggInsights(): array
    {
        $limit = (int) config('dss.eggs.max_batches', 5);

        $records = Penetasan::query()
            ->with('kandang')
            ->orderByDesc('tanggal_simpan_telur')
            ->take($limit * 2)
            ->get();

        if ($records->isEmpty()) {
            return [];
        }

        return $records
            ->map(function (Penetasan $record) {
                $hatcherDate = $record->tanggal_masuk_hatcher ?? $record->target_hatcher_date;
                $daysToHatcher = $hatcherDate
                    ? $this->today->copy()->startOfDay()->diffInDays($hatcherDate, false)
                    : null;

                $hatchRate = $this->resolveHatchRate($record);
                $status = $this->evaluateEggStatus($daysToHatcher, $hatchRate);

                return [
                    'batch' => $record->batch ?? ('Batch #' . $record->id),
                    'kandang' => optional($record->kandang)->nama_kandang ?? '-',
                    'fase' => $record->fase_penetasan ?? '-',
                    'jumlah_telur' => (int) ($record->jumlah_telur ?? 0),
                    'jumlah_menetas' => (int) ($record->jumlah_menetas ?? 0),
                    'hatch_rate' => $hatchRate,
                    'days_to_hatcher' => $daysToHatcher,
                    'target_hatcher' => $hatcherDate ? $hatcherDate->format('d/m/Y') : null,
                    'status' => $status,
                ];
            })
            ->filter()
            ->take($limit)
            ->values()
            ->all();
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

    protected function buildMortalityAlerts(): array
    {
        $windowDays = max(1, (int) config('dss.mortality.window_days', 3));
        $limit = (int) config('dss.mortality.max_items', 4);
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

        $warningPct = (float) config('dss.mortality.warning_pct', 3);
        $criticalPct = (float) config('dss.mortality.critical_pct', 5);

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

        $ratio = abs($deltaKg) / max($targetKg, 0.0001);
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

        return [
            'level' => $level,
            'message' => $message,
            'direction' => $direction,
            'delta_pct' => $percentage,
            'recommendation' => $this->resolveFeedRecommendation($level, $direction),
            'severity' => $level === 'critical' ? 2 : ($level === 'warning' ? 1 : 0),
        ];
    }

    protected function resolveFeedRecommendation(string $level, string $direction): string
    {
        if ($level === 'critical') {
            return $direction === 'over'
                ? 'Evaluasi kembali jadwal pemberian pakan dan cek sisa pakan di kandang.'
                : 'Pastikan pasokan pakan lancar dan cek kesehatan ternak.';
        }

        if ($level === 'warning') {
            return $direction === 'over'
                ? 'Koreksi dosis agar tidak ada pemborosan.'
                : 'Tingkatkan monitoring konsumsi harian.';
        }

        return 'Tidak ada tindakan khusus, lanjutkan pemantauan harian.';
    }

    protected function resolveHatchRate(Penetasan $record): ?float
    {
        if (!is_null($record->persentase_tetas)) {
            return (float) $record->persentase_tetas;
        }

        if (($record->jumlah_telur ?? 0) > 0) {
            return round((($record->jumlah_menetas ?? 0) / $record->jumlah_telur) * 100, 2);
        }

        return null;
    }

    protected function evaluateEggStatus(?int $daysToHatcher, ?float $hatchRate): array
    {
        $warningDays = (int) config('dss.eggs.hatcher_warning_days', 2);
        $criticalDays = (int) config('dss.eggs.hatcher_critical_days', 0);
        $warningRate = (float) config('dss.eggs.hatch_rate_warning', 85);
        $criticalRate = (float) config('dss.eggs.hatch_rate_critical', 70);

        $level = 'ok';
        $messages = [];

        if ($daysToHatcher === null) {
            $level = 'warning';
            $messages[] = 'Tanggal masuk hatcher belum ditentukan.';
        } else {
            if ($daysToHatcher <= $criticalDays) {
                $level = 'critical';
                $messages[] = 'Segera pindahkan ke hatcher (jadwal sudah tiba).';
            } elseif ($daysToHatcher <= $warningDays) {
                $level = $level === 'critical' ? 'critical' : 'warning';
                $messages[] = 'Hatcher due dalam ' . $daysToHatcher . ' hari.';
            } else {
                $messages[] = 'Hatcher due dalam ' . $daysToHatcher . ' hari.';
            }
        }

        if ($hatchRate !== null) {
            if ($hatchRate <= $criticalRate) {
                $level = 'critical';
                $messages[] = 'Tingkat tetas hanya ' . $hatchRate . '%. Perlu investigasi.';
            } elseif ($hatchRate <= $warningRate && $level !== 'critical') {
                $level = 'warning';
                $messages[] = 'Tingkat tetas di bawah target (' . $hatchRate . '%).';
            } else {
                $messages[] = 'Tingkat tetas ' . $hatchRate . '%. Stabil.';
            }
        } else {
            $messages[] = 'Belum ada data tingkat tetas.';
        }

        return [
            'level' => $level,
            'message' => implode(' ', $messages),
        ];
    }
}
