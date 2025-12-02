<?php

namespace App\Http\Controllers;

use App\Services\Dss\DssInsightService;
use App\Services\Dss\DssSettingsService;
use App\Services\Ml\DssModelService;
use Illuminate\Support\Arr;

class DssController extends Controller
{
    public function index(
        DssInsightService $insightService,
        DssSettingsService $settingsService,
        DssModelService $mlService
    ) {
        $settings = $settingsService->getSettings();
        $mode = $settings['mode'] ?? 'config';
        $insights = [];
        $summary = [];
        $mlResponse = null;

        if ($mode === 'ml') {
            $payload = [
                'phase' => Arr::get($settings, 'ml.default_phase', 'grower'),
                'metrics' => Arr::get($settings, 'ml.metrics', []),
                'metadata' => [
                    'artifact_label' => Arr::get($settings, 'ml.artifact_label'),
                    'notes' => Arr::get($settings, 'ml.notes'),
                ],
            ];

            $mlResponse = $mlService->predict($payload);
        } else {
            $insights = $insightService->getInsights();

            $summary = [
                'eggs' => $this->summarizeSection($insights['eggs'] ?? [], 'status.level'),
                'feed' => $this->summarizeSection($insights['feed'] ?? [], 'status.level'),
                'mortality' => $this->summarizeSection($insights['mortality'] ?? [], 'status'),
            ];
        }

        return view('admin.pages.dss.index', [
            'insights' => $insights,
            'summary' => $summary,
            'lastUpdated' => now(),
            'dssMode' => $mode,
            'settings' => $settings,
            'mlResponse' => $mlResponse,
        ]);
    }

    protected function summarizeSection(array $items, string $statusPath): array
    {
        $collection = collect($items);
        $alertCount = $collection
            ->filter(function ($item) use ($statusPath) {
                $value = data_get($item, $statusPath);
                return in_array($value, ['warning', 'critical'], true);
            })
            ->count();

        return [
            'total' => $collection->count(),
            'alerts' => $alertCount,
        ];
    }
}
