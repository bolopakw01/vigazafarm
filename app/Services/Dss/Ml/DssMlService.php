<?php

namespace App\Services\Dss\Ml;

use App\Models\Kematian;
use App\Models\Pakan;
use App\Models\PencatatanProduksi;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class DssMlService
{
    protected Carbon $today;

    public function __construct()
    {
        $this->today = now();
    }

    public function buildDashboardPayload(array $settings = []): array
    {
        $capabilities = Arr::get($settings, 'ml.capabilities', []);

        $eggForecast = $this->featureEnabled($capabilities, 'egg_forecast')
            ? $this->forecastEggProduction()
            : null;
        $feedPrediction = $this->featureEnabled($capabilities, 'feed_prediction')
            ? $this->predictFeedNeeds()
            : null;
        $mortalityDetection = $this->featureEnabled($capabilities, 'mortality_detection')
            ? $this->detectMortalityAnomalies()
            : null;
        $pricingOptimization = $this->featureEnabled($capabilities, 'pricing_optimizer')
            ? $this->optimizePricing($eggForecast, $feedPrediction)
            : null;

        $alerts = $this->compileAlerts($eggForecast, $feedPrediction, $mortalityDetection);
        $recommendations = $this->compileRecommendations($eggForecast, $feedPrediction, $mortalityDetection, $pricingOptimization);

        $status = collect([$eggForecast, $feedPrediction, $mortalityDetection, $pricingOptimization])
            ->filter()
            ->isEmpty() ? 'waiting_for_data' : 'ready';

        $metadata = [
            'generated_at' => $this->today->toDateTimeString(),
            'default_phase' => Arr::get($settings, 'ml.default_phase', 'grower'),
            'window_days' => [
                'eggs' => Arr::get($eggForecast, 'window_days'),
                'feed' => Arr::get($feedPrediction, 'window_days'),
                'mortality' => Arr::get($mortalityDetection, 'window_days'),
            ],
            'records_used' => collect([
                Arr::get($eggForecast, 'series_count', 0),
                Arr::get($feedPrediction, 'series_count', 0),
                Arr::get($mortalityDetection, 'series_count', 0),
            ])->sum(),
        ];

        return [
            'status' => $status,
            'model_version' => Arr::get($settings, 'ml.artifact_label', 'vf-ml-sim-1'),
            'metadata' => $metadata,
            'predictions' => [
                'eggs' => $eggForecast,
                'feed' => $feedPrediction,
                'mortality' => $mortalityDetection,
                'pricing' => $pricingOptimization,
            ],
            'alerts' => $alerts,
            'recommendations' => $recommendations,
        ];
    }

    protected function forecastEggProduction(): ?array
    {
        $window = 14;
        $startDate = $this->today->copy()->subDays($window - 1)->startOfDay();

        $dailyRows = PencatatanProduksi::query()
            ->selectRaw('DATE(tanggal) as tanggal, COALESCE(SUM(jumlah_produksi), 0) as total, AVG(harga_per_unit) as avg_price')
            ->whereBetween('tanggal', [$startDate->toDateString(), $this->today->toDateString()])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $series = [];
        $x = [];
        $y = [];
        $cursor = $startDate->copy();
        $index = 0;

        while ($cursor <= $this->today) {
            $dateKey = $cursor->toDateString();
            $value = (float) ($dailyRows[$dateKey]->total ?? 0);
            $series[] = [
                'date' => $cursor->format('d M'),
                'value' => round($value, 2),
            ];
            $x[] = $index;
            $y[] = $value;
            $cursor->addDay();
            $index++;
        }

        $seriesCount = count($series);
        $hasSignal = collect($y)->sum() > 0;

        $linear = $hasSignal
            ? $this->linearForecast($x, $y)
            : [
                'forecast' => 0,
                'slope' => 0,
                'confidence' => 0.35,
            ];

        $trend = 'flat';
        if ($linear['slope'] > 5) {
            $trend = 'up';
        } elseif ($linear['slope'] < -5) {
            $trend = 'down';
        }

        $drivers = $this->resolveProductionDrivers($startDate, $this->today);
        $avgPrice = $dailyRows->values()->avg('avg_price');

        return [
            'forecast' => round(max(0, $linear['forecast']), 0),
            'trend' => $trend,
            'slope' => round($linear['slope'], 2),
            'confidence' => round($linear['confidence'], 2),
            'window_days' => $window,
            'series' => $series,
            'series_count' => $seriesCount,
            'drivers' => $drivers,
            'avg_price' => $avgPrice ? round($avgPrice, 2) : null,
        ];
    }

    protected function predictFeedNeeds(): ?array
    {
        $window = 7;
        $startDate = $this->today->copy()->subDays($window - 1)->startOfDay();

        $grouped = Pakan::query()
            ->whereNotNull('batch_produksi_id')
            ->whereBetween('tanggal', [$startDate->toDateString(), $this->today->toDateString()])
            ->selectRaw('batch_produksi_id, DATE(tanggal) as tanggal, COALESCE(SUM(jumlah_kg), 0) as total, AVG(harga_per_kg) as price')
            ->groupBy('batch_produksi_id', 'tanggal')
            ->get()
            ->groupBy('batch_produksi_id');

        if ($grouped->isEmpty()) {
            return null;
        }

        $perBatch = [];
        $totalRequired = 0;

        foreach ($grouped as $batchId => $items) {
            $ordered = $items->sortBy('tanggal')->values();
            $avgDaily = round($ordered->avg('total'), 2);
            $latest = $ordered->last();
            $latestValue = (float) ($latest->total ?? 0);
            $delta = round($latestValue - $avgDaily, 2);
            $status = $this->classifyDelta($delta, $avgDaily);
            $recommendation = $this->feedRecommendation($status);
            $series = $ordered->map(function ($row) {
                return [
                    'date' => Carbon::parse($row->tanggal)->format('d/m'),
                    'value' => round((float) $row->total, 2),
                ];
            })->all();
            $required = round(max($avgDaily, $latestValue), 2);
            $totalRequired += $required;

            $perBatch[] = [
                'batch' => $batchId,
                'avg_daily' => $avgDaily,
                'latest' => $latestValue,
                'delta' => $delta,
                'status' => $status,
                'recommendation' => $recommendation,
                'series' => $series,
                'required_kg' => $required,
            ];
        }

        $perBatch = collect($perBatch)
            ->sortByDesc('required_kg')
            ->values()
            ->all();

        $avgPricePerKg = Pakan::query()
            ->whereBetween('tanggal', [$startDate->toDateString(), $this->today->toDateString()])
            ->avg('harga_per_kg');

        return [
            'window_days' => $window,
            'total_required_kg' => round($totalRequired, 2),
            'avg_price_per_kg' => $avgPricePerKg ? round($avgPricePerKg, 2) : null,
            'series_count' => count($perBatch) * $window,
            'per_batch' => $perBatch,
        ];
    }

    protected function detectMortalityAnomalies(): ?array
    {
        $window = 7;
        $startDate = $this->today->copy()->subDays($window - 1)->startOfDay();

        $grouped = Kematian::query()
            ->whereNotNull('batch_produksi_id')
            ->whereBetween('tanggal', [$startDate->toDateString(), $this->today->toDateString()])
            ->selectRaw('batch_produksi_id, DATE(tanggal) as tanggal, COALESCE(SUM(jumlah), 0) as total')
            ->groupBy('batch_produksi_id', 'tanggal')
            ->get()
            ->groupBy('batch_produksi_id');

        if ($grouped->isEmpty()) {
            return null;
        }

        $alerts = [];

        foreach ($grouped as $batchId => $items) {
            $ordered = $items->sortBy('tanggal')->values();
            $values = $ordered->pluck('total')->all();
            $avg = $ordered->avg('total');
            $std = $this->stdDev($values);
            $latestValue = (float) ($ordered->last()->total ?? 0);
            $score = $std > 0 ? ($latestValue - $avg) / $std : 0;
            $risk = 'low';

            if ($score >= 2) {
                $risk = 'critical';
            } elseif ($score >= 1) {
                $risk = 'warning';
            }

            $alerts[] = [
                'batch' => $batchId,
                'latest' => (int) $latestValue,
                'average' => round($avg, 2),
                'score' => round($score, 2),
                'risk' => $risk,
                'series' => $ordered->map(function ($row) {
                    return [
                        'date' => Carbon::parse($row->tanggal)->format('d/m'),
                        'value' => (int) $row->total,
                    ];
                })->all(),
                'message' => $risk === 'critical'
                    ? 'Lonjakan mortalitas signifikan, lakukan investigasi biosekuriti.'
                    : ($risk === 'warning'
                        ? 'Ada peningkatan mortalitas, periksa gejala klinis.'
                        : 'Fluktuasi dalam batas normal.'),
            ];
        }

        $alerts = collect($alerts)
            ->sortByDesc('score')
            ->values()
            ->all();

        return [
            'window_days' => $window,
            'alerts' => $alerts,
            'series_count' => count($alerts) * $window,
        ];
    }

    protected function optimizePricing(?array $eggForecast, ?array $feedPrediction): ?array
    {
        $window = 14;
        $startDate = $this->today->copy()->subDays($window - 1)->startOfDay();

        $priceStats = PencatatanProduksi::query()
            ->whereBetween('tanggal', [$startDate->toDateString(), $this->today->toDateString()])
            ->selectRaw('AVG(harga_per_unit) as avg_price, MIN(harga_per_unit) as min_price, MAX(harga_per_unit) as max_price')
            ->first();

        $basePrice = (float) ($priceStats->avg_price ?? 0);
        $forecastVolume = (float) Arr::get($eggForecast, 'forecast', 0);

        if ($basePrice <= 0 && $forecastVolume <= 0) {
            return null;
        }

        $series = collect(Arr::get($eggForecast, 'series', []));
        $avgHistorical = $series->avg('value') ?? $forecastVolume;
        $supplyPressure = $avgHistorical > 0 ? $forecastVolume / $avgHistorical : 1.0;

        $adjustmentFactor = 1.0;
        if ($supplyPressure > 1.05) {
            $adjustmentFactor -= min(0.15, ($supplyPressure - 1) * 0.1);
        } elseif ($supplyPressure < 0.95) {
            $adjustmentFactor += min(0.1, (1 - $supplyPressure) * 0.08);
        }

        $optimalPrice = round(max(0, ($basePrice ?: 1500) * $adjustmentFactor), 2);
        $feedRequired = (float) Arr::get($feedPrediction, 'total_required_kg', 0);
        $avgFeedPrice = (float) Arr::get($feedPrediction, 'avg_price_per_kg', 0);
        $expectedFeedCost = round($feedRequired * $avgFeedPrice, 2);
        $expectedRevenue = round($forecastVolume * $optimalPrice, 2);
        $expectedProfit = round(max(0, $expectedRevenue - $expectedFeedCost), 2);

        return [
            'window_days' => $window,
            'optimal_price' => $optimalPrice,
            'price_band' => [
                'min' => round($priceStats->min_price ?? ($optimalPrice * 0.9), 2),
                'max' => round($priceStats->max_price ?? ($optimalPrice * 1.1), 2),
            ],
            'expected_revenue' => $expectedRevenue,
            'expected_feed_cost' => $expectedFeedCost,
            'expected_profit' => $expectedProfit,
            'supply_pressure' => round($supplyPressure, 2),
            'notes' => $supplyPressure > 1
                ? 'Produksi diprediksi meningkat, turunkan harga untuk menjaga perputaran.'
                : 'Produksi stabil atau turun, harga dapat dinaikkan secara bertahap.',
        ];
    }

    protected function compileAlerts(?array $eggForecast, ?array $feedPrediction, ?array $mortalityDetection): array
    {
        $alerts = [];

        if ($eggForecast && Arr::get($eggForecast, 'trend') === 'down') {
            $alerts[] = [
                'level' => 'warning',
                'title' => 'Prediksi produksi telur menurun',
                'detail' => 'Slope ' . Arr::get($eggForecast, 'slope') . ' butir/hari dengan prediksi ' . number_format((float) Arr::get($eggForecast, 'forecast')) . ' butir.',
                'tags' => ['eggs', 'forecast'],
            ];
        }

        if ($feedPrediction) {
            foreach (Arr::get($feedPrediction, 'per_batch', []) as $batch) {
                if (in_array($batch['status'], ['over', 'under'])) {
                    $alerts[] = [
                        'level' => $batch['status'] === 'over' ? 'warning' : 'info',
                        'title' => 'Batch ' . $batch['batch'] . ' ' . ($batch['status'] === 'over' ? 'kelebihan' : 'kekurangan') . ' pakan',
                        'detail' => 'Perbedaan ' . $batch['delta'] . ' kg terhadap rata-rata harian.',
                        'tags' => ['feed', 'ops'],
                    ];
                }
            }
        }

        if ($mortalityDetection) {
            foreach (Arr::get($mortalityDetection, 'alerts', []) as $alert) {
                if ($alert['risk'] === 'low') {
                    continue;
                }

                $alerts[] = [
                    'level' => $alert['risk'] === 'critical' ? 'critical' : 'warning',
                    'title' => 'Mortalitas batch ' . $alert['batch'],
                    'detail' => $alert['message'],
                    'tags' => ['mortality', 'health'],
                ];
            }
        }

        return collect($alerts)
            ->unique(fn ($alert) => $alert['title'])
            ->values()
            ->all();
    }

    protected function compileRecommendations(?array $eggForecast, ?array $feedPrediction, ?array $mortalityDetection, ?array $pricing): array
    {
        $recommendations = [];

        if ($eggForecast) {
            $recommendations[] = [
                'category' => 'Produksi Telur',
                'summary' => 'Prediksi ' . number_format((float) $eggForecast['forecast']) . ' butir/hari (' . strtoupper($eggForecast['trend']) . ').',
                'action_items' => [
                    'Sinkronkan jadwal panen dengan kapasitas grader.',
                    'Validasi kembali input pakan untuk batch penyumbang terbesar.',
                ],
            ];
        }

        if ($feedPrediction) {
            $recommendations[] = [
                'category' => 'Kebutuhan Pakan',
                'summary' => 'Total kebutuhan harian ' . number_format((float) $feedPrediction['total_required_kg'], 2) . ' kg.',
                'action_items' => [
                    'Sesuaikan distribusi pakan pada batch dengan status tidak stabil.',
                    'Kunci harga pembelian pakan bila tren biaya naik.',
                ],
            ];
        }

        if ($mortalityDetection) {
            $highRisk = collect($mortalityDetection['alerts'] ?? [])->firstWhere('risk', 'critical');
            if ($highRisk) {
                $recommendations[] = [
                    'category' => 'Mortalitas',
                    'summary' => 'Batch ' . $highRisk['batch'] . ' menunjukkan skor risiko ' . $highRisk['score'] . '.',
                    'action_items' => [
                        'Lakukan investigasi kesehatan dan cek biosekuriti.',
                        'Catat kronologi dan isolasi gejala klinis.',
                    ],
                ];
            }
        }

        if ($pricing) {
            $recommendations[] = [
                'category' => 'Harga Jual',
                'summary' => 'Harga optimal Rp ' . number_format((float) $pricing['optimal_price'], 2) . ' dengan profit ' . number_format((float) $pricing['expected_profit'], 0),
                'action_items' => [
                    'Diskusikan dengan tim sales untuk menerapkan rentang harga.' ,
                    'Monitor biaya pakan agar margin tetap sesuai proyeksi.',
                ],
            ];
        }

        if (empty($recommendations)) {
            $recommendations[] = [
                'category' => 'Model ML',
                'summary' => 'Belum ada cukup data untuk menghasilkan rekomendasi otomatis.',
                'action_items' => [
                    'Pastikan pencatatan produksi, pakan, dan mortalitas terisi 14 hari terakhir.',
                    'Latih ulang model setelah data mencukupi.',
                ],
            ];
        }

        return $recommendations;
    }

    protected function resolveProductionDrivers(Carbon $startDate, Carbon $endDate): array
    {
        $drivers = PencatatanProduksi::query()
            ->with('produksi.kandang')
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('produksi_id, COALESCE(SUM(jumlah_produksi), 0) as total')
            ->groupBy('produksi_id')
            ->orderByDesc('total')
            ->take(3)
            ->get();

        return $drivers->filter(fn ($row) => $row->produksi_id)
            ->map(function ($row) {
                $label = optional(optional($row->produksi)->kandang)->nama_kandang;
                if (!$label) {
                    $label = 'Produksi #' . $row->produksi_id;
                }

                return [
                    'produksi_id' => $row->produksi_id,
                    'label' => $label,
                    'value' => (int) $row->total,
                ];
            })
            ->values()
            ->all();
    }

    protected function linearForecast(array $x, array $y): array
    {
        $n = count($x);
        if ($n === 0 || $n !== count($y)) {
            return ['forecast' => 0, 'slope' => 0, 'confidence' => 0.35];
        }

        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumXX = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumXX += $x[$i] ** 2;
        }

        $denominator = ($n * $sumXX) - ($sumX ** 2);
        $slope = $denominator !== 0 ? (($n * $sumXY) - ($sumX * $sumY)) / $denominator : 0;
        $intercept = ($sumY - ($slope * $sumX)) / ($n ?: 1);
        $forecast = $intercept + $slope * $n;

        $rmse = 0;
        for ($i = 0; $i < $n; $i++) {
            $prediction = $intercept + ($slope * $x[$i]);
            $rmse += ($y[$i] - $prediction) ** 2;
        }
        $rmse = $n > 0 ? sqrt($rmse / $n) : 0;
        $baseline = ($sumY / ($n ?: 1)) ?: 1;
        $confidence = max(0.35, 1 - min(0.9, $rmse / $baseline));

        return [
            'forecast' => $forecast,
            'slope' => $slope,
            'confidence' => $confidence,
        ];
    }

    protected function stdDev(array $values): float
    {
        $n = count($values);
        if ($n === 0) {
            return 0.0;
        }

        $mean = array_sum($values) / $n;
        $variance = 0.0;
        foreach ($values as $value) {
            $variance += ($value - $mean) ** 2;
        }

        return sqrt($variance / $n);
    }

    protected function classifyDelta(float $delta, float $avg): string
    {
        $threshold = max(0.5, $avg * 0.15);
        if ($delta > $threshold) {
            return 'over';
        }
        if ($delta < -$threshold) {
            return 'under';
        }
        return 'steady';
    }

    protected function feedRecommendation(string $status): string
    {
        return match ($status) {
            'over' => 'Periksa distribusi dan hindari overfeeding pada batch ini.',
            'under' => 'Tambahkan feeding session ekstra untuk mengejar target.',
            default => 'Konsumsi stabil, lanjutkan monitoring.',
        };
    }

    protected function featureEnabled(array $capabilities, string $key): bool
    {
        if (!array_key_exists($key, $capabilities)) {
            return true;
        }

        return (bool) $capabilities[$key];
    }
}
