<?php

namespace App\Http\Controllers;

use App\Models\Pembesaran;
use App\Models\Penetasan;
use App\Models\Produksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SistemController extends Controller
{
    protected string $goalsStorage = 'dashboard_goals.json';

    public function index()
    {
        return view('admin.pages.sistem.index');
    }

    public function dashboard()
    {
        $goals = $this->loadDashboardGoals();

        return view('admin.pages.sistem.dashboard.setgoals', compact('goals'));
    }

    public function updateDashboard(Request $request)
    {
        $validated = $request->validate([
            'goals' => 'present|array',
            'goals.*.title' => 'required|string|max:100',
            'goals.*.key' => 'required|string|in:produksi,penetasan,pembesaran,user|distinct',
            'goals.*.current' => 'nullable|integer|min:0',
            'goals.*.target' => 'required|integer|min:1',
            'goals.*.unit' => 'nullable|string|max:50',
            'goals.*.color' => ['nullable', 'regex:/^#([0-9a-fA-F]{3}){1,2}$/'],
            'goals.*.icon' => 'nullable|string|max:50',
        ]);

        $goalsInput = collect($validated['goals'])
            ->map(function ($goal) {
                $goal['key'] = $goal['key'] ?? Str::slug($goal['title'], '_');
                return $goal;
            })
            ->toArray();

        $goals = $this->normalizeGoals($goalsInput);

        Storage::disk('local')->put(
            $this->goalsStorage,
            json_encode($goals, JSON_PRETTY_PRINT)
        );

        $hydratedGoals = $this->applyLiveMetrics($goals);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Target dashboard berhasil diperbarui!',
                'goals' => $hydratedGoals,
            ]);
        }

        return redirect()->route('admin.sistem.dashboard')
                        ->with('success', 'Target dashboard berhasil diperbarui!');
    }

    public function getDashboardGoals()
    {
        return $this->applyLiveMetrics($this->loadDashboardGoals());
    }

    protected function loadDashboardGoals(): array
    {
        $defaultGoals = [
            [
                'key' => 'produksi',
                'title' => 'Produksi',
                'current' => 160,
                'target' => 200,
                'unit' => 'butir',
                'color' => '#2563eb',
                'icon' => 'fas fa-egg',
            ],
            [
                'key' => 'penetasan',
                'title' => 'Penetasan',
                'current' => 310,
                'target' => 400,
                'unit' => 'doc',
                'color' => '#7c3aed',
                'icon' => 'fas fa-baby',
            ],
            [
                'key' => 'pembesaran',
                'title' => 'Pembesaran',
                'current' => 480,
                'target' => 800,
                'unit' => 'ekor',
                'color' => '#059669',
                'icon' => 'fas fa-seedling',
            ],
            [
                'key' => 'user',
                'title' => 'User',
                'current' => 250,
                'target' => 500,
                'unit' => 'akun',
                'color' => '#f97316',
                'icon' => 'fas fa-users',
            ],
        ];

        if (!Storage::disk('local')->exists($this->goalsStorage)) {
            return $defaultGoals;
        }

        $stored = json_decode(Storage::disk('local')->get($this->goalsStorage), true);

        return is_array($stored) ? $this->normalizeGoals($stored) : $defaultGoals;
    }

    protected function normalizeGoals(array $goals): array
    {
        return collect($goals)
            ->map(function ($goal) {
                return [
                    'key' => $goal['key'] ?? Str::slug($goal['title'] ?? 'goal', '_'),
                    'title' => $goal['title'] ?? 'Goal',
                    'current' => (int) ($goal['current'] ?? 0),
                    'target' => max((int) ($goal['target'] ?? 1), 1),
                    'unit' => $goal['unit'] ?? '',
                    'color' => $goal['color'] ?? '#2563eb',
                    'icon' => $goal['icon'] ?? 'fas fa-chart-line',
                ];
            })
            ->values()
            ->all();
    }

    protected function applyLiveMetrics(array $goals): array
    {
        $resolvers = $this->liveMetricResolvers();
        $resolvedValues = [];

        return collect($goals)
            ->map(function ($goal) use ($resolvers, &$resolvedValues) {
                $key = $goal['key'] ?? null;

                if ($key && isset($resolvers[$key])) {
                    if (!array_key_exists($key, $resolvedValues)) {
                        $resolvedValues[$key] = (int) $resolvers[$key]();
                    }

                    $goal['current'] = $resolvedValues[$key];
                }

                return $goal;
            })
            ->values()
            ->all();
    }

    protected function liveMetricResolvers(): array
    {
        return [
            'produksi' => fn () => Produksi::count(),
            'penetasan' => fn () => Penetasan::count(),
            'pembesaran' => fn () => Pembesaran::count(),
            'user' => fn () => User::count(),
        ];
    }
}