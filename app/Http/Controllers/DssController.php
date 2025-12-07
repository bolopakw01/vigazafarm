<?php

namespace App\Http\Controllers;

use App\Models\Penetasan;
use App\Services\Dss\DssInsightService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DssController extends Controller
{
    public function __construct(private DssInsightService $dssInsightService)
    {
    }

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
            'feed' => $this->getFeedInsights(),
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

        $today = now()->startOfDay();
        $insights = [];

        foreach ($hatchings as $hatching) {
            $targetHatcherDate = $hatching->target_hatcher_date?->copy()->startOfDay();
            $daysToHatcher = $targetHatcherDate ? $today->diffInDays($targetHatcherDate, false) : null;

            $stageInfo = $this->resolveHatchingStage($hatching, $eggSettings, $today);
            $statusLevel = $stageInfo['level'];
            $messages = [$stageInfo['message']];
            $hatchRate = $this->calculateHatchRate($hatching);

            if ($stageInfo['show_hatch_rate']) {
                if (!is_null($hatchRate)) {
                    if ($hatchRate <= $eggSettings['hatch_rate_critical']) {
                        $statusLevel = 'critical';
                        $messages[] = 'Rasio tetas ' . $hatchRate . '% berada di bawah batas kritis.';
                    } elseif ($hatchRate <= $eggSettings['hatch_rate_warning'] && $statusLevel !== 'critical') {
                        $statusLevel = $statusLevel === 'critical' ? 'critical' : 'warning';
                        $messages[] = 'Rasio tetas ' . $hatchRate . '% berada di bawah standar.';
                    } else {
                        $messages[] = 'Rasio tetas ' . $hatchRate . '% stabil.';
                    }
                } else {
                    $messages[] = 'Rasio tetas belum tercatat.';
                }
            } else {
                // $messages[] = '- config by Loopa';
            }

            $insights[] = [
                'batch' => $hatching->batch,
                'kandang' => $hatching->kandang->nama_kandang ?? 'Unknown',
                'fase' => ucfirst($hatching->fase_penetasan),
                'target_hatcher' => $hatching->target_hatcher_date?->format('d/m/Y'),
                'days_to_hatcher' => $daysToHatcher,
                'hatch_rate' => $stageInfo['show_hatch_rate'] ? $hatchRate : null,
                'jumlah_menetas' => $hatching->jumlah_menetas ?? 0,
                'jumlah_telur' => $hatching->jumlah_telur ?? 0,
                'status' => [
                    'level' => $statusLevel,
                    'stage' => $stageInfo['stage'],
                    'message' => trim(collect($messages)->filter()->implode(' ')),
                ],
            ];
        }

        return $insights;
    }

    private function getFeedInsights(): array
    {
        return $this->dssInsightService->getFeedInsights();
    }

    private function getMortalityInsights($mortalitySettings)
    {
        return $this->dssInsightService->getMortalityAlerts();
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

    private function resolveHatchingStage(Penetasan $hatching, array $eggSettings, Carbon $today): array
    {
        $fase = strtolower($hatching->fase_penetasan ?? 'setter');
        $statusValue = strtolower($hatching->status ?? 'proses');
        $targetHatcher = $hatching->target_hatcher_date?->copy()->startOfDay();
        $warningDays = (int) ($eggSettings['hatcher_warning_days'] ?? 2);
        $criticalDays = (int) ($eggSettings['hatcher_critical_days'] ?? 0);

        if ($statusValue === 'selesai') {
            return [
                'stage' => 'Penetasan Selesai',
                'level' => 'ok',
                'message' => 'Batch telah selesai menetas.',
                'show_hatch_rate' => true,
            ];
        }

        if ($fase === 'hatcher') {
            $readyToFinish = $this->isReadyForDoq($hatching, $today);

            if ($readyToFinish) {
                $expectedFinish = $this->resolveExpectedFinishDate($hatching);
                $daysLate = $expectedFinish ? $expectedFinish->copy()->startOfDay()->diffInDays($today, false) : 0;

                return [
                    'stage' => 'Masuk DOQ dan Selesaikan',
                    'level' => $daysLate > 1 ? 'critical' : 'warning',
                    'message' => $daysLate > 1
                        ? 'DOQ tertahan ' . $daysLate . ' hari, segera selesaikan batch ini.'
                        : 'DOQ siap dipindahkan, selesaikan batch ini.',
                    'show_hatch_rate' => false,
                ];
            }

            return [
                'stage' => 'Menunggu Waktu Menetas',
                'level' => 'info',
                'message' => 'Telur berada di hatcher, pantau sampai waktunya menetas.',
                'show_hatch_rate' => false,
            ];
        }

        if ($hatching->jumlah_doc > 0 && $hatching->fase_penetasan === 'setter' && $hatching->tanggal_menetas) {
            return [
                'stage' => 'Masuk DOQ dan Selesaikan',
                'level' => 'warning',
                'message' => 'DOQ tersedia namun status belum diselesaikan, lengkapi proses.',
                'show_hatch_rate' => true,
            ];
        }

        if (!$targetHatcher) {
            return [
                'stage' => 'Menunggu Jadwal Hatcher',
                'level' => 'warning',
                'message' => 'Belum ada jadwal hatcher. Tetapkan tanggal target.',
                'show_hatch_rate' => false,
            ];
        }

        $daysUntil = $today->diffInDays($targetHatcher, false);

        if ($daysUntil <= $criticalDays) {
            $lateDays = abs($daysUntil);

            return [
                'stage' => 'Konfirmasi Pindah Setter ke Hatcher',
                'level' => 'critical',
                'message' => $lateDays > 0
                    ? 'Jadwal hatcher terlambat ' . $lateDays . ' hari, segera pindahkan.'
                    : 'Jadwal hatcher jatuh hari ini, segera pindahkan.',
                'show_hatch_rate' => false,
            ];
        }

        if ($daysUntil <= $warningDays) {
            return [
                'stage' => 'Konfirmasi Pindah Setter ke Hatcher',
                'level' => 'warning',
                'message' => 'Hatcher due dalam ' . max(0, $daysUntil) . ' hari, siapkan perpindahan.',
                'show_hatch_rate' => false,
            ];
        }

        return [
            'stage' => 'Menunggu Jadwal Hatcher',
            'level' => 'info',
            'message' => 'Pindah hatcher terjadwal dalam ' . $daysUntil . ' hari.',
            'show_hatch_rate' => false,
        ];
    }

    private function calculateHatchRate(Penetasan $hatching): ?float
    {
        if (!is_null($hatching->persentase_tetas)) {
            return (float) $hatching->persentase_tetas;
        }

        if (($hatching->jumlah_telur ?? 0) > 0) {
            return round((($hatching->jumlah_menetas ?? 0) / $hatching->jumlah_telur) * 100, 2);
        }

        return null;
    }

    private function isReadyForDoq(Penetasan $hatching, Carbon $today): bool
    {
        $expectedFinish = $this->resolveExpectedFinishDate($hatching);

        return $expectedFinish ? $today->gte($expectedFinish->copy()->startOfDay()) : false;
    }

    private function resolveExpectedFinishDate(Penetasan $hatching): ?Carbon
    {
        if ($hatching->status === 'selesai' && $hatching->tanggal_menetas) {
            return $hatching->tanggal_menetas->copy();
        }

        if ($hatching->estimasi_tanggal_menetas) {
            return $hatching->estimasi_tanggal_menetas->copy();
        }

        if ($hatching->tanggal_menetas) {
            return $hatching->tanggal_menetas->copy();
        }

        if ($hatching->tanggal_simpan_telur) {
            $start = $hatching->tanggal_simpan_telur instanceof Carbon
                ? $hatching->tanggal_simpan_telur->copy()
                : Carbon::parse($hatching->tanggal_simpan_telur);

            return $start->addDays(17);
        }

        return null;
    }
}
