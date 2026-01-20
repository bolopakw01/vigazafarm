<?php

namespace App\Services\Dss;

use App\Models\Kematian;
use App\Models\LaporanHarian;
use App\Models\Pakan;
use App\Models\Pembesaran;
use App\Models\Penetasan;
use App\Models\Produksi;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

    public function getFeedInsights(): array
    {
        return $this->buildFeedInsights();
    }

    public function getMortalityAlerts(): array
    {
        return $this->buildMortalityAlerts();
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
                    'batch' => $record->batch_label ?? ('Batch #' . $record->id),
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

        $pembesaranRecords = Pembesaran::query()
            ->with('kandang')
            ->where(function ($query) {
                $query->whereNull('status_batch')
                    ->orWhereNotIn('status_batch', ['selesai', 'closed', 'selesai_transfer']);
            })
            ->orderByDesc('tanggal_masuk')
            ->take($limit * 3)
            ->get();

        $produksiRecords = Produksi::query()
            ->with(['kandang', 'pembesaran.kandang'])
            ->whereNotNull('batch_produksi_id')
            ->where('tipe_produksi', 'puyuh')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereNotIn('status', ['selesai', 'closed']);
            })
            ->orderByDesc('tanggal_mulai')
            ->take($limit * 3)
            ->get();

        $contexts = collect();

        foreach ($pembesaranRecords as $batch) {
            if (empty($batch->batch_produksi_id)) {
                continue;
            }

            $contexts->put($batch->batch_produksi_id, [
                'type' => 'pembesaran',
                'record' => $batch,
                'sort_key' => $batch->tanggal_masuk ?? $batch->created_at ?? $this->today,
            ]);
        }

        foreach ($produksiRecords as $production) {
            if (empty($production->batch_produksi_id)) {
                continue;
            }

            $contexts->put($production->batch_produksi_id, [
                'type' => 'produksi',
                'record' => $production,
                'sort_key' => $production->tanggal_mulai ?? $production->created_at ?? $this->today,
            ]);
        }

        if ($contexts->isEmpty()) {
            return [];
        }

        $sorter = function ($context) {
            $value = $context['sort_key'];
            if ($value instanceof Carbon) {
                return $value->timestamp;
            }

            if ($value) {
                try {
                    return Carbon::parse($value)->timestamp;
                } catch (\Throwable $e) {
                    return now()->timestamp;
                }
            }

            return now()->timestamp;
        };

        $sorted = $contexts->values()
            ->sortByDesc($sorter)
            ->values();

        $selected = $sorted->take($limit)->values();

        $ensureSegmentCoverage = function (string $segment) use (&$selected, $sorted, $limit, $sorter) {
            $hasSegment = $selected->contains(fn ($context) => $context['type'] === $segment);
            $segmentAvailable = $sorted->contains(fn ($context) => $context['type'] === $segment);

            if ($hasSegment || !$segmentAvailable) {
                return;
            }

            $replacement = $sorted->first(fn ($context) => $context['type'] === $segment);
            if (!$replacement) {
                return;
            }

            $selectedArray = $selected->all();
            $removed = false;

            for ($i = count($selectedArray) - 1; $i >= 0; $i--) {
                if ($selectedArray[$i]['type'] !== $segment) {
                    array_splice($selectedArray, $i, 1);
                    $removed = true;
                    break;
                }
            }

            if (!$removed && count($selectedArray) >= $limit) {
                return;
            }

            $selectedArray[] = $replacement;
            $selected = collect($selectedArray)
                ->sortByDesc($sorter)
                ->take($limit)
                ->values();
        };

        $ensureSegmentCoverage('produksi');
        $ensureSegmentCoverage('pembesaran');

        $batchIds = $selected
            ->map(fn ($context) => $context['record']->batch_produksi_id)
            ->filter()
            ->values();

        $pembesaranFallback = Pembesaran::query()
            ->with('kandang')
            ->whereIn('batch_produksi_id', $batchIds)
            ->get()
            ->keyBy('batch_produksi_id');

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

        return $selected->map(function ($context) use ($feedHistoryTotals, $feedTodayTotals, $totalDeaths, $historyDays, $pembesaranFallback) {
            $record = $context['record'];
            $type = $context['type'];
            $batchId = $record->batch_produksi_id;
            $fallbackRecord = $record instanceof Produksi
                ? ($record->pembesaran ?? $pembesaranFallback->get($batchId))
                : $pembesaranFallback->get($batchId);

            $umurSource = $record;
            if ($type === 'produksi' && is_null($this->resolveUmurHari($record)) && $fallbackRecord) {
                $umurSource = $fallbackRecord;
            }

            $popSource = $record;
            if ($type === 'produksi' && $this->resolvePopulasiAwal($record) === 0 && $fallbackRecord) {
                $popSource = $fallbackRecord;
            }

            $umurHari = $this->resolveUmurHari($umurSource);
            $phase = $this->determinePhase($umurHari);
            $phaseLabel = Arr::get($phase, 'label', $type === 'produksi' ? 'Produksi' : 'Growth');
            $targetPerBird = (float) Arr::get($phase, 'target_feed_per_bird_kg', 0.02);

            $populasiAwal = $this->resolvePopulasiAwal($popSource);
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
                'batch' => optional($record->batchProduksi)->kode_batch ?? $batchId,
                'kandang' => optional($record->kandang)->nama_kandang
                    ?? optional(optional($fallbackRecord)->kandang)->nama_kandang
                    ?? 'Kandang ' . ($record->kandang_id ?? optional($fallbackRecord)->kandang_id ?? '?'),
                'fase' => $phaseLabel,
                'segment' => $type,
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
        $startDate = $this->today->copy()->subDays($windowDays - 1);

        $recentRows = Kematian::query()
            ->where('tanggal', '>=', $startDate->toDateString())
            ->where(function ($query) {
                $query->whereNotNull('batch_produksi_id')
                    ->orWhereNotNull('produksi_id');
            })
            ->selectRaw('batch_produksi_id, produksi_id, COALESCE(SUM(jumlah), 0) as total')
            ->groupBy('batch_produksi_id', 'produksi_id')
            ->orderByDesc('total')
            ->take($limit * 6)
            ->get();

        $productionIds = $recentRows->pluck('produksi_id')->filter()->unique();

        $productionLookup = $productionIds->isNotEmpty()
            ? Produksi::query()
                ->with(['kandang', 'pembesaran.kandang'])
                ->whereIn('id', $productionIds)
                ->where('tipe_produksi', 'puyuh')
                ->get()
                ->keyBy('id')
            : collect();

        $recentTotals = [];

        foreach ($recentRows as $row) {
            $production = $row->produksi_id ? $productionLookup->get($row->produksi_id) : null;
            $batchLookupKey = $row->batch_produksi_id ?? ($production?->batch_produksi_id);
            $displayBatch = $batchLookupKey
                ?? ($production?->batch_produksi_id)
                ?? ($row->produksi_id ? ('Produksi #' . $row->produksi_id) : null);

            if (!$row->produksi_id && !$batchLookupKey) {
                continue;
            }

            if ($row->produksi_id && $batchLookupKey) {
                $key = 'batch:' . $batchLookupKey;
            } elseif ($row->produksi_id) {
                $key = 'produksi:' . $row->produksi_id;
            } else {
                $key = 'batch:' . $batchLookupKey;
            }

            if (!isset($recentTotals[$key])) {
                $recentTotals[$key] = [
                    'key' => $key,
                    'batch_lookup_key' => $batchLookupKey,
                    'display_batch' => $displayBatch,
                    'recent_total' => 0,
                    'production' => $production,
                    'production_id' => $row->produksi_id,
                    'context' => $row->produksi_id ? 'produksi' : 'pembesaran',
                ];
            }

            $recentTotals[$key]['recent_total'] += (int) $row->total;

            if ($row->produksi_id && $production) {
                $recentTotals[$key]['production'] = $production;
                $recentTotals[$key]['context'] = 'produksi';

                if (empty($recentTotals[$key]['display_batch'])) {
                    $recentTotals[$key]['display_batch'] = $production->batch_produksi_id
                        ?? ('Produksi #' . $production->id);
                }

                if (empty($recentTotals[$key]['batch_lookup_key'])) {
                    $recentTotals[$key]['batch_lookup_key'] = $production->batch_produksi_id;
                }
            }

        }

        $laporanRows = LaporanHarian::query()
            ->where('tanggal', '>=', $startDate->toDateString())
            ->where('jumlah_kematian', '>', 0)
            ->selectRaw('batch_produksi_id, COALESCE(SUM(jumlah_kematian), 0) as total')
            ->groupBy('batch_produksi_id')
            ->orderByDesc('total')
            ->take($limit * 6)
            ->get();

        foreach ($laporanRows as $row) {
            $batchLookupKey = $row->batch_produksi_id;
            if (!$batchLookupKey) {
                continue;
            }

            $key = 'batch:' . $batchLookupKey;

            if (!isset($recentTotals[$key])) {
                $recentTotals[$key] = [
                    'key' => $key,
                    'batch_lookup_key' => $batchLookupKey,
                    'display_batch' => $batchLookupKey,
                    'recent_total' => 0,
                    'production' => null,
                    'production_id' => null,
                    'context' => null,
                ];
            }

            $recentTotals[$key]['recent_total'] += (int) $row->total;
            $recentTotals[$key]['laporan_total'] = ($recentTotals[$key]['laporan_total'] ?? 0) + (int) $row->total;
        }

        if (empty($recentTotals)) {
            return [];
        }

        $batchLookupKeys = collect($recentTotals)
            ->pluck('batch_lookup_key')
            ->filter()
            ->unique()
            ->all();

        $pembesaranMap = Pembesaran::whereIn('batch_produksi_id', $batchLookupKeys)
            ->with('kandang')
            ->get()
            ->keyBy('batch_produksi_id');

        $produksiCollection = Produksi::query()
            ->with(['kandang', 'pembesaran.kandang'])
            ->where(function ($query) use ($batchLookupKeys, $productionIds) {
                $query->whereIn('batch_produksi_id', $batchLookupKeys);
                if ($productionIds->isNotEmpty()) {
                    $query->orWhereIn('id', $productionIds);
                }
            })
            ->where('tipe_produksi', 'puyuh')
            ->get();

        $produksiMapByBatch = $produksiCollection->filter(fn ($item) => !empty($item->batch_produksi_id))
            ->keyBy('batch_produksi_id');
        $productionLookup = $productionLookup->union($produksiCollection->keyBy('id'));

        foreach ($recentTotals as $key => &$payload) {
            if (($payload['context'] ?? null) === 'produksi') {
                continue;
            }

            $batchKey = $payload['batch_lookup_key'] ?? null;
            if (!$batchKey) {
                continue;
            }

            if ($produksiMapByBatch->has($batchKey)) {
                $production = $produksiMapByBatch->get($batchKey);
                $payload['context'] = 'produksi';
                $payload['production'] = $production;
                $payload['production_id'] = $payload['production_id'] ?? $production->id;
                continue;
            }

            if (Str::startsWith(Str::upper($batchKey), 'PROD')) {
                $payload['context'] = 'produksi';
            }
        }
        unset($payload);

        $totalDeathRows = Kematian::query()
            ->where(function ($query) use ($batchLookupKeys, $productionIds) {
                $query->whereIn('batch_produksi_id', $batchLookupKeys);
                if ($productionIds->isNotEmpty()) {
                    $query->orWhereIn('produksi_id', $productionIds);
                }
            })
            ->selectRaw('batch_produksi_id, produksi_id, COALESCE(SUM(jumlah), 0) as total')
            ->groupBy('batch_produksi_id', 'produksi_id')
            ->get();

        $totalDeathsByKey = [];

        foreach ($totalDeathRows as $row) {
            $production = $row->produksi_id ? $productionLookup->get($row->produksi_id) : null;
            $batchId = $row->batch_produksi_id ?? ($production?->batch_produksi_id);

            if ($row->produksi_id) {
                $key = 'produksi:' . $row->produksi_id;
            } elseif ($batchId) {
                $key = 'batch:' . $batchId;
            } else {
                continue;
            }

            $totalDeathsByKey[$key] = ($totalDeathsByKey[$key] ?? 0) + (int) $row->total;
        }

        foreach ($recentTotals as $entry) {
            if (!empty($entry['laporan_total'])) {
                $totalDeathsByKey[$entry['key']] = ($totalDeathsByKey[$entry['key']] ?? 0) + (int) $entry['laporan_total'];
            }
        }

        $warningPct = (float) config('dss.mortality.warning_pct', 3);
        $criticalPct = (float) config('dss.mortality.critical_pct', 5);
        $dateRange = [
            'start' => $startDate->format('d/m/Y'),
            'end' => $this->today->format('d/m/Y'),
        ];

        $recentCollection = collect(array_values($recentTotals))
            ->sortByDesc(fn ($item) => $item['recent_total'])
            ->values();

        $selected = $recentCollection->take($limit)->values();

        $ensureContextCoverage = function (string $context) use (&$selected, $recentCollection, $limit) {
            $hasContext = $selected->contains(fn ($item) => $item['context'] === $context);
            $contextAvailable = $recentCollection->contains(fn ($item) => $item['context'] === $context);

            if ($hasContext || !$contextAvailable) {
                return;
            }

            $replacement = $recentCollection->first(fn ($item) => $item['context'] === $context);
            if (!$replacement) {
                return;
            }

            $selectedArray = $selected->all();
            $removed = false;

            for ($i = count($selectedArray) - 1; $i >= 0; $i--) {
                if ($selectedArray[$i]['context'] !== $context) {
                    array_splice($selectedArray, $i, 1);
                    $removed = true;
                    break;
                }
            }

            if (!$removed && count($selectedArray) >= $limit) {
                return;
            }

            $selectedArray[] = $replacement;
            $selected = collect($selectedArray)
                ->sortByDesc(fn ($item) => $item['recent_total'])
                ->take($limit)
                ->values();
        };

        $ensureContextCoverage('produksi');
        $ensureContextCoverage('pembesaran');

        return $selected
            ->map(function ($data) use ($pembesaranMap, $produksiMapByBatch, $productionLookup, $totalDeathsByKey, $warningPct, $criticalPct, $windowDays, $dateRange) {
                $lookupKey = $data['batch_lookup_key'];
                $key = $data['key'];
                $productionRef = $data['production']
                    ?? (!empty($data['production_id']) ? $productionLookup->get($data['production_id']) : null);
                $batch = ($lookupKey ? ($pembesaranMap->get($lookupKey)
                    ?? $produksiMapByBatch->get($lookupKey)) : null)
                    ?? $productionRef;

                $recentTotal = (int) $data['recent_total'];
                if ($recentTotal <= 0) {
                    return null;
                }

                $batchLabel = $data['display_batch']
                    ?? ($lookupKey
                        ?? ($productionRef?->batch_produksi_id
                            ?? ($data['context'] === 'produksi'
                                ? 'Produksi #' . ($data['production_id'] ?? '?')
                                : 'Batch tidak diketahui')));

                if (!$batch) {
                    return [
                        'batch' => $batchLabel,
                        'kandang' => $data['context'] === 'produksi' ? 'Kandang produksi' : 'Kandang belum terdata',
                        'fase' => $data['context'] === 'produksi' ? 'Produksi' : 'Growth',
                        'date_range' => $dateRange,
                        'total_kematian' => $recentTotal,
                        'mortality_rate' => '-',
                        'standard_rate' => $warningPct,
                        'status' => [
                            'level' => 'info',
                            'message' => 'Detail batch tidak ditemukan, namun tercatat ' . $recentTotal . ' ekor.',
                        ],
                        'recommendation' => 'Lengkapi data batch untuk menghitung deviasi mortalitas.',
                    ];
                }

                $fallbackBatch = $batch instanceof Produksi
                    ? ($batch->pembesaran ?? ($lookupKey ? $pembesaranMap->get($lookupKey) : null))
                    : ($lookupKey ? $pembesaranMap->get($lookupKey) : null);

                $populationSource = $batch;
                if ($this->resolvePopulasiAwal($populationSource) === 0 && $fallbackBatch) {
                    $populationSource = $fallbackBatch;
                }

                $populasiAwal = $this->resolvePopulasiAwal($populationSource);
                $totalDeathsForKey = (int) ($totalDeathsByKey[$key] ?? $recentTotal);

                $umurSource = $batch;
                if (is_null($this->resolveUmurHari($umurSource)) && $fallbackBatch) {
                    $umurSource = $fallbackBatch;
                }

                $umurHari = $this->resolveUmurHari($umurSource);
                $phase = $this->determinePhase($umurHari);
                $faseLabel = Arr::get($phase, 'label', $data['context'] === 'produksi' ? 'Produksi' : 'Growth');

                if ($populasiAwal <= 0) {
                    $statusLevel = 'info';
                    $statusMessage = 'Populasi awal belum tercatat, ' . $recentTotal . ' ekor tercatat dalam ' . $windowDays . ' hari.';
                    $recommendation = 'Lengkapi data populasi agar persentase mortalitas dapat dihitung.';
                    $mortalitasPctDisplay = '-';
                } else {
                    $populasiSaatIni = max(0, $populasiAwal - $totalDeathsForKey);
                    if ($populasiSaatIni <= 0) {
                        $populasiSaatIni = $populasiAwal;
                    }

                    $mortalitasPctValue = round(($recentTotal / max($populasiSaatIni, 1)) * 100, 2);
                    $statusLevel = 'ok';

                    if ($mortalitasPctValue >= $criticalPct) {
                        $statusLevel = 'critical';
                    } elseif ($mortalitasPctValue >= $warningPct) {
                        $statusLevel = 'warning';
                    }

                    $statusMessage = $statusLevel === 'ok'
                        ? 'Mortalitas dalam batas wajar (' . $mortalitasPctValue . '%)'
                        : 'Mortalitas ' . $mortalitasPctValue . '% dalam ' . $windowDays . ' hari';

                    $recommendation = match ($statusLevel) {
                        'critical' => 'Segera lakukan investigasi kesehatan dan cek biosecurity.',
                        'warning' => 'Perketat monitoring harian dan review SOP pemberian pakan/vitamin.',
                        default => 'Tidak ada deviasi berarti, lanjutkan monitoring rutin.',
                    };

                    $mortalitasPctDisplay = $mortalitasPctValue;
                }

                return [
                    'batch' => optional($batch->batchProduksi)->kode_batch ?? $batchLabel,
                    'kandang' => optional($batch->kandang)->nama_kandang
                        ?? optional(optional($fallbackBatch)->kandang)->nama_kandang
                        ?? 'Kandang ' . ($batch->kandang_id ?? optional($fallbackBatch)->kandang_id ?? '?'),
                    'fase' => $faseLabel,
                    'date_range' => $dateRange,
                    'total_kematian' => $recentTotal,
                    'mortality_rate' => $mortalitasPctDisplay,
                    'standard_rate' => $warningPct,
                    'status' => [
                        'level' => $statusLevel,
                        'message' => $statusMessage,
                    ],
                    'recommendation' => $recommendation,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function resolveUmurHari(Pembesaran|Produksi|null $batch): ?int
    {
        if (!$batch) {
            return null;
        }

        if ($batch instanceof Pembesaran) {
            if (!empty($batch->umur_hari)) {
                return (int) $batch->umur_hari;
            }

            if ($batch->tanggal_masuk) {
                return Carbon::parse($batch->tanggal_masuk)->diffInDays($this->today) + 1;
            }

            return null;
        }

        if (!empty($batch->umur_mulai_produksi)) {
            return (int) $batch->umur_mulai_produksi;
        }

        if ($batch->tanggal_mulai) {
            return Carbon::parse($batch->tanggal_mulai)->diffInDays($this->today) + 1;
        }

        if ($batch->pembesaran) {
            return $this->resolveUmurHari($batch->pembesaran);
        }

        return null;
    }

    protected function resolvePopulasiAwal(Pembesaran|Produksi|null $batch): int
    {
        if (!$batch) {
            return 0;
        }

        if ($batch instanceof Pembesaran) {
            if (!empty($batch->jumlah_siap)) {
                return (int) $batch->jumlah_siap;
            }

            if (!empty($batch->jumlah_anak_ayam)) {
                return (int) $batch->jumlah_anak_ayam;
            }

            return 0;
        }

        if (!empty($batch->jumlah_indukan)) {
            return (int) $batch->jumlah_indukan;
        }

        $genderSum = (int) ($batch->jumlah_jantan ?? 0) + (int) ($batch->jumlah_betina ?? 0);
        if ($genderSum > 0) {
            return $genderSum;
        }

        if ($batch->pembesaran) {
            return $this->resolvePopulasiAwal($batch->pembesaran);
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

    public function getTrendSeries(int $days = 7): array
    {
        $days = max(3, min(30, $days));
        $endDate = $this->today->copy()->endOfDay();
        $startDate = $endDate->copy()->subDays($days - 1)->startOfDay();

        $dateKeys = [];
        $labels = [];
        for ($i = 0; $i < $days; $i++) {
            $current = $startDate->copy()->addDays($i);
            $dateKeys[] = $current->toDateString();
            $labels[] = $current->format('d M');
        }

        $eggRows = Penetasan::query()
            ->whereNotNull('tanggal_menetas')
            ->whereBetween('tanggal_menetas', [$startDate->copy(), $endDate->copy()])
            ->selectRaw('DATE(tanggal_menetas) as tanggal, COALESCE(SUM(jumlah_menetas), 0) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $feedRows = Pakan::query()
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('tanggal as tanggal, COALESCE(SUM(jumlah_kg), 0) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $mortalityRows = Kematian::query()
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('tanggal as tanggal, COALESCE(SUM(jumlah), 0) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $seriesData = [
            'eggs' => $this->buildTrendSeriesFromRows($dateKeys, $eggRows, fn ($row) => (float) ($row->total ?? 0)),
            'feed' => $this->buildTrendSeriesFromRows($dateKeys, $feedRows, fn ($row) => round((float) ($row->total ?? 0), 2)),
            'mortality' => $this->buildTrendSeriesFromRows($dateKeys, $mortalityRows, fn ($row) => (float) ($row->total ?? 0)),
        ];

        $seriesMeta = [
            'eggs' => ['label' => 'Penetasan Telur', 'unit' => 'butir', 'color' => '#f97316'],
            'feed' => ['label' => 'Konsumsi Pakan', 'unit' => 'kg', 'color' => '#0ea5e9'],
            'mortality' => ['label' => 'Kematian', 'unit' => 'ekor', 'color' => '#dc2626'],
        ];

        $hasData = collect($seriesData)
            ->flatten()
            ->some(fn ($value) => (float) $value !== 0.0);

        return [
            'labels' => $labels,
            'data' => $seriesData,
            'meta' => $seriesMeta,
            'date_range' => [
                'start' => $startDate->format('d M Y'),
                'end' => $endDate->format('d M Y'),
            ],
            'has_data' => $hasData,
        ];
    }

    protected function buildTrendSeriesFromRows(array $dateKeys, iterable $rows, callable $valueResolver): array
    {
        $lookup = [];
        foreach ($rows as $row) {
            $rawDate = is_array($row) ? ($row['tanggal'] ?? null) : ($row->tanggal ?? null);
            if (!$rawDate) {
                continue;
            }

            try {
                $dateKey = Carbon::parse($rawDate)->toDateString();
            } catch (\Throwable $exception) {
                continue;
            }

            $lookup[$dateKey] = $valueResolver($row);
        }

        return array_map(static function ($dateKey) use ($lookup) {
            return round((float) ($lookup[$dateKey] ?? 0), 2);
        }, $dateKeys);
    }
}
