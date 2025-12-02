<?php

namespace App\Http\Controllers;

use App\Models\Kematian;
use App\Models\Pakan;
use App\Models\Penetasan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DssController extends Controller
{
    public function index(Request $request)
    {
        $dssMode = $request->get('mode', 'config');
        $settings = config('dss');

        if ($dssMode === 'ml') {
            // ML response - to be implemented with actual ML service
            $mlResponse = [
                'recommendations' => [],
                'metadata' => [],
                'status' => 'waiting_for_training',
                'model_version' => 'untrained'
            ];

            return view('admin.pages.dss.index', compact('dssMode', 'mlResponse', 'settings'));
        }

        // Config mode
        $insights = $this->getInsights($settings);
        $summary = $this->getSummary($insights);
        $lastUpdated = now();

        return view('admin.pages.dss.index', compact('dssMode', 'insights', 'summary', 'lastUpdated', 'settings'));
    }

    private function getInsights($settings)
    {
        return [
            'eggs' => $this->getEggInsights($settings['eggs']),
            'feed' => $this->getFeedInsights($settings['feed']),
            'mortality' => $this->getMortalityInsights($settings['mortality']),
        ];
    }

    private function getEggInsights($eggSettings)
    {
        $hatchings = Penetasan::with('kandang')
            ->where('fase_penetasan', '!=', 'siap')
            ->orderBy('tanggal_simpan_telur', 'desc')
            ->limit($eggSettings['max_batches'])
            ->get();

        $insights = [];
        foreach ($hatchings as $hatching) {
            $daysToHatcher = null;
            if ($hatching->target_hatcher_date) {
                $daysToHatcher = now()->diffInDays($hatching->target_hatcher_date, false);
            }

            $hatchRate = null;
            $statusLevel = 'info';
            $statusMessage = 'Sedang dipantau';

            if ($hatching->fase_penetasan === 'hatcher') {
                $hatchRate = $hatching->persentase_tetas ?? 0;
                if ($hatchRate < $eggSettings['hatch_rate_critical']) {
                    $statusLevel = 'critical';
                    $statusMessage = 'Rasio tetas sangat rendah';
                } elseif ($hatchRate < $eggSettings['hatch_rate_warning']) {
                    $statusLevel = 'warning';
                    $statusMessage = 'Rasio tetas di bawah standar';
                } else {
                    $statusLevel = 'ok';
                    $statusMessage = 'Rasio tetas dalam batas normal';
                }
            } elseif ($hatching->fase_penetasan === 'setter') {
                if ($daysToHatcher !== null) {
                    if ($daysToHatcher <= $eggSettings['hatcher_critical_days']) {
                        $statusLevel = 'critical';
                        $statusMessage = 'Segera masuk hatcher';
                    } elseif ($daysToHatcher <= $eggSettings['hatcher_warning_days']) {
                        $statusLevel = 'warning';
                        $statusMessage = 'Hampir waktunya masuk hatcher';
                    } else {
                        $statusLevel = 'ok';
                        $statusMessage = 'Masih dalam fase setter';
                    }
                }
            }

            $insights[] = [
                'batch' => $hatching->batch,
                'kandang' => $hatching->kandang->nama_kandang ?? 'Unknown',
                'fase' => ucfirst($hatching->fase_penetasan),
                'target_hatcher' => $hatching->target_hatcher_date?->format('d/m/Y'),
                'days_to_hatcher' => $daysToHatcher,
                'hatch_rate' => $hatchRate,
                'jumlah_menetas' => $hatching->jumlah_menetas ?? 0,
                'jumlah_telur' => $hatching->jumlah_telur ?? 0,
                'status' => [
                    'level' => $statusLevel,
                    'message' => $statusMessage,
                ],
            ];
        }

        return $insights;
    }

    private function getFeedInsights($feedSettings)
    {
        // TODO: Implement actual query to Pakan and Pembesaran models
        $insights = [];

        return $insights;
    }

    private function getMortalityInsights($mortalitySettings)
    {
        // TODO: Implement actual query to Kematian model
        $insights = [];

        return $insights;
    }

    private function getSummary($insights)
    {
        $summary = [];
        foreach (['eggs', 'feed', 'mortality'] as $key) {
            $data = $insights[$key] ?? [];
            $alerts = collect($data)->filter(function ($item) {
                return in_array($item['status']['level'], ['warning', 'critical']);
            })->count();

            $summary[$key] = [
                'total' => count($data),
                'alerts' => $alerts,
            ];
        }

        return $summary;
    }
}
