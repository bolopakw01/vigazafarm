<?php

namespace App\Http\Controllers;

use App\Services\Dss\DssInsightService;

class DssController extends Controller
{
    public function index(DssInsightService $insightService)
    {
        $insights = $insightService->getInsights();

        $summary = [
            'feed' => $this->summarizeSection($insights['feed'] ?? [], 'status.level'),
            'stock' => $this->summarizeSection($insights['stock'] ?? [], 'status'),
            'environment' => $this->summarizeSection($insights['environment'] ?? [], 'status'),
            'health' => $this->summarizeSection($insights['health'] ?? [], 'status'),
        ];

        return view('admin.pages.dss.index', [
            'insights' => $insights,
            'summary' => $summary,
            'lastUpdated' => now(),
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
