<?php

namespace App\Services\Dss;

use App\Models\Kematian;
use App\Models\MlOutput;
use App\Models\MlRun;
use App\Models\Pakan;
use App\Models\Pembesaran;
use App\Models\PencatatanProduksi;
use App\Models\Produksi;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MlEngine
{
    /**
     * Jalankan analitik ML ringan (statistik/forecast sederhana) berbasis data aktual.
     */
    public function run(?string $label = null): MlRun
    {
        return DB::transaction(function () use ($label) {
            $run = MlRun::create([
                'status' => 'running',
                'label' => $label,
                'started_at' => Carbon::now(),
            ]);

            try {
                [$activeBatchIds, $activeProduksiIds] = $this->getActiveBatchAndProduksiIds();

                $eggOutputs = $this->forecastEggs($activeProduksiIds, $run->id);
                $feedOutputs = $this->estimateFeed($activeBatchIds, $run->id);
                $mortalityOutputs = $this->detectMortality($activeBatchIds, $run->id);
                $priceOutputs = $this->recommendPrice($eggOutputs, $feedOutputs, $run->id);
                $summaryOutputs = $this->buildSummary($eggOutputs, $feedOutputs, $mortalityOutputs, $priceOutputs, $run->id);

                MlOutput::insert(array_merge($eggOutputs, $feedOutputs, $mortalityOutputs, $priceOutputs, $summaryOutputs));

                $run->update([
                    'status' => 'success',
                    'finished_at' => Carbon::now(),
                    'meta' => [
                        'counts' => [
                            'egg' => count($eggOutputs),
                            'feed' => count($feedOutputs),
                            'mortality' => count($mortalityOutputs),
                            'price' => count($priceOutputs),
                            'summary' => count($summaryOutputs),
                        ],
                    ],
                ]);
            } catch (\Throwable $e) {
                $run->update([
                    'status' => 'failed',
                    'finished_at' => Carbon::now(),
                    'error_message' => $e->getMessage(),
                ]);

                throw $e;
            }

            return $run;
        });
    }

    protected function forecastEggs(array $produksiIds, int $runId): array
    {
        if (empty($produksiIds)) {
            return [];
        }

        $now = Carbon::now();
        $startWindow = $now->copy()->subDays(28)->toDateString();

        $historical = PencatatanProduksi::query()
            ->whereIn('produksi_id', $produksiIds)
            ->where('tanggal', '>=', $startWindow)
            ->get(['produksi_id', 'tanggal', 'jumlah_produksi']);

        $grouped = $historical->groupBy('produksi_id');
        $outputs = [];

        foreach ($grouped as $produksiId => $rows) {
            $series = $rows->sortBy('tanggal')->pluck('jumlah_produksi');
            $baseline = $series->isEmpty() ? 0 : $series->takeLast(7)->avg();
            $baseline = $baseline > 0 ? $baseline : $series->avg();
            $baseline = $baseline ?: 0;

            for ($h = 1; $h <= 7; $h++) {
                $date = $now->copy()->addDays($h);
                $trendAdj = 1 + ($h * 0.01); // kecil
                $pred = round($baseline * $trendAdj, 2);
                $outputs[] = [
                    'run_id' => $runId,
                    'type' => 'egg_forecast',
                    'entity_type' => 'produksi',
                    'entity_id' => $produksiId,
                    'tanggal_prediksi' => $date->toDateString(),
                    'horizon' => $h,
                    'nilai' => $pred,
                    'lower' => round($pred * 0.9, 2),
                    'upper' => round($pred * 1.1, 2),
                    'score' => null,
                    'status_flag' => 'normal',
                    'top_features' => json_encode(['lag7_mean' => round($baseline, 2)]),
                    'meta' => json_encode(['method' => 'mean_7d_trend']),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        return $outputs;
    }

    protected function estimateFeed(array $batchIds, int $runId): array
    {
        if (empty($batchIds)) {
            return [];
        }

        $now = Carbon::now();
        $startWindow = $now->copy()->subDays(14)->toDateString();

        $feed = Pakan::query()
            ->whereIn('batch_produksi_id', $batchIds)
            ->where('tanggal', '>=', $startWindow)
            ->select('batch_produksi_id', 'tanggal', 'jumlah_kg')
            ->get()
            ->groupBy('batch_produksi_id');

        $outputs = [];
        foreach ($feed as $batchId => $rows) {
            $baseline = $rows->pluck('jumlah_kg')->takeLast(7)->avg();
            $baseline = $baseline ?: $rows->avg();
            $baseline = $baseline ?: 0;

            for ($h = 1; $h <= 7; $h++) {
                $date = $now->copy()->addDays($h);
                $pred = round($baseline, 2);
                $outputs[] = [
                    'run_id' => $runId,
                    'type' => 'feed_need',
                    'entity_type' => 'batch',
                    'entity_id' => $batchId,
                    'tanggal_prediksi' => $date->toDateString(),
                    'horizon' => $h,
                    'nilai' => $pred,
                    'lower' => round($pred * 0.9, 2),
                    'upper' => round($pred * 1.1, 2),
                    'score' => null,
                    'status_flag' => 'normal',
                    'top_features' => json_encode(['avg7d_feed_kg' => round($baseline, 2)]),
                    'meta' => json_encode(['method' => 'mean_7d_hold']),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        return $outputs;
    }

    protected function detectMortality(array $batchIds, int $runId): array
    {
        if (empty($batchIds)) {
            return [];
        }

        $now = Carbon::now();
        $startWindow = $now->copy()->subDays(21)->toDateString();

        $rows = Kematian::query()
            ->whereIn('batch_produksi_id', $batchIds)
            ->where('tanggal', '>=', $startWindow)
            ->select('batch_produksi_id', 'tanggal', 'jumlah')
            ->get()
            ->groupBy('batch_produksi_id');

        $outputs = [];
        foreach ($rows as $batchId => $items) {
            $sorted = $items->sortBy('tanggal');
            $values = $sorted->pluck('jumlah');
            $mean = $values->avg() ?: 0;
            $std = $this->stddev($values);
            $latest = $values->last() ?? 0;
            $z = ($std > 0) ? ($latest - $mean) / $std : 0;
            $flag = $z >= 2.5 ? 'alert' : 'normal';

            $outputs[] = [
                'run_id' => $runId,
                'type' => 'mortality_anomaly',
                'entity_type' => 'batch',
                'entity_id' => $batchId,
                'tanggal_prediksi' => $sorted->last()?->tanggal,
                'horizon' => 0,
                'nilai' => $latest,
                'lower' => null,
                'upper' => null,
                'score' => round($z, 4),
                'status_flag' => $flag,
                'top_features' => json_encode(['mean' => round($mean, 2), 'std' => round($std, 2)]),
                'meta' => json_encode(['window_days' => 21]),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $outputs;
    }

    protected function recommendPrice(array $eggOutputs, array $feedOutputs, int $runId): array
    {
        $now = Carbon::now();
        if (empty($eggOutputs)) {
            return [];
        }

        // Map feed per batch to cost per egg if possible (rough)
        $feedByBatch = collect($feedOutputs)
            ->groupBy('entity_id')
            ->map(fn (Collection $rows) => $rows->avg('nilai'));

        $outputs = [];
        foreach (collect($eggOutputs)->groupBy('entity_id') as $produksiId => $rows) {
            $forecastAvg = collect($rows)->avg('nilai') ?: 0;
            $basePrice = Produksi::find($produksiId)?->harga_per_pcs ?? 450;
            $batchId = Produksi::find($produksiId)?->batch_produksi_id;
            $feedCost = $batchId ? ($feedByBatch->get($batchId) ?? 0) : 0;
            $costPerEgg = $forecastAvg > 0 ? $feedCost / max($forecastAvg, 1) : 0;
            $recPrice = max($basePrice, ($costPerEgg * 1.25) + 50);

            $outputs[] = [
                'run_id' => $runId,
                'type' => 'price_rec',
                'entity_type' => 'produksi',
                'entity_id' => (int) $produksiId,
                'tanggal_prediksi' => $now->toDateString(),
                'horizon' => 0,
                'nilai' => round($recPrice, 2),
                'lower' => round($recPrice * 0.95, 2),
                'upper' => round($recPrice * 1.05, 2),
                'score' => null,
                'status_flag' => 'normal',
                'top_features' => json_encode([
                    'forecast_avg' => round($forecastAvg, 2),
                    'feed_cost_per_batch' => round($feedCost, 2),
                    'base_price' => $basePrice,
                ]),
                'meta' => json_encode(['method' => 'cost_plus']),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $outputs;
    }

    protected function buildSummary(array $egg, array $feed, array $mortality, array $price, int $runId): array
    {
        $now = Carbon::now();
        $summary = [];

        $eggAvg = collect($egg)->avg('nilai') ?: 0;
        $feedAvg = collect($feed)->avg('nilai') ?: 0;
        $alerts = collect($mortality)->where('status_flag', 'alert')->count();
        $priceAvg = collect($price)->avg('nilai') ?: 0;

        $text = sprintf(
            'Egg forecast avg %.0f | Feed need avg %.2f kg | Price rec avg Rp %.0f | Mortality alerts: %d',
            $eggAvg,
            $feedAvg,
            $priceAvg,
            $alerts
        );

        $summary[] = [
            'run_id' => $runId,
            'type' => 'summary',
            'entity_type' => null,
            'entity_id' => null,
            'tanggal_prediksi' => $now->toDateString(),
            'horizon' => 0,
            'nilai' => null,
            'lower' => null,
            'upper' => null,
            'score' => null,
            'status_flag' => $alerts > 0 ? 'alert' : 'normal',
            'top_features' => json_encode(['alerts' => $alerts]),
            'meta' => json_encode(['text' => $text]),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        return $summary;
    }

    protected function stddev(Collection $values): float
    {
        $n = $values->count();
        if ($n <= 1) {
            return 0.0;
        }

        $mean = $values->avg();
        $variance = $values->sum(function ($x) use ($mean) {
            return pow($x - $mean, 2);
        }) / ($n - 1);

        return sqrt($variance);
    }

    protected function activeStatusValues(): array
    {
        return [
            'aktif',
            'active',
            'berjalan',
            'proses',
            'ongoing',
            'sedang berjalan',
            'running',
            'in_progress',
        ];
    }

    protected function getActiveBatchAndProduksiIds(): array
    {
        $activeStatuses = $this->activeStatusValues();

        $activeProduksi = Produksi::query()
            ->whereIn(DB::raw('LOWER(COALESCE(status, ""))'), $activeStatuses)
            ->get(['id', 'batch_produksi_id']);

        $activeProduksiIds = $activeProduksi->pluck('id')->unique()->values()->all();
        $activeProductionBatchIds = $activeProduksi->pluck('batch_produksi_id')->filter()->unique()->values()->all();

        $activeGrowerBatchIds = Pembesaran::query()
            ->whereIn(DB::raw('LOWER(COALESCE(status_batch, ""))'), $activeStatuses)
            ->pluck('batch_produksi_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $activeBatchIds = collect($activeProductionBatchIds)
            ->merge($activeGrowerBatchIds)
            ->unique()
            ->values()
            ->all();

        return [$activeBatchIds, $activeProduksiIds];
    }
}
