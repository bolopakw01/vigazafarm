<?php

namespace App\Http\Controllers;

use App\Models\Kesehatan;
use App\Models\Pakan;
use App\Models\Pembesaran;
use App\Models\Penetasan;
use App\Models\PencatatanProduksi;
use App\Models\Produksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SistemController extends Controller
{
    protected string $goalsStorage = 'dashboard_goals.json';
    protected string $matrixStorage = 'dashboard_matrix.json';
    protected float $matrixEqualityTolerance = 500;

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

    public function matrix()
    {
        $targets = $this->loadMatrixTargets();
        $metrics = $this->calculateFinancialMetrics();
        $snapshot = $this->buildMatrixSnapshot($targets, $metrics);

        return view('admin.pages.sistem.dashboard.setmatriks', compact('targets', 'metrics', 'snapshot'));
    }

    public function updateMatrix(Request $request)
    {
        $validated = $request->validate([
            'targets' => 'required|array',
            'targets.pendapatan' => 'required|numeric|min:0',
            'targets.pengeluaran' => 'required|numeric|min:0',
            'targets.laba' => 'required|numeric|min:0',
        ]);

        $targets = $this->normalizeMatrixTargets($validated['targets']);

        Storage::disk('local')->put(
            $this->matrixStorage,
            json_encode($targets, JSON_PRETTY_PRINT)
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Target matriks berhasil diperbarui!',
                'snapshot' => $this->getMatrixSnapshot(),
            ]);
        }

        return redirect()->route('admin.sistem.matriks')
                        ->with('success', 'Target matriks berhasil diperbarui!');
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

    public function getMatrixSnapshot(): array
    {
        $targets = $this->loadMatrixTargets();
        $metrics = $this->calculateFinancialMetrics();

        $snapshot = $this->buildMatrixSnapshot($targets, $metrics);

        return $this->appendGoalsMatrixCard($snapshot);
    }

    protected function defaultMatrixTargets(): array
    {
        return [
            'pendapatan' => [
                'key' => 'pendapatan',
                'label' => 'Pendapatan',
                'target' => 35000000,
                'icon' => 'fa-solid fa-coins',
            ],
            'pengeluaran' => [
                'key' => 'pengeluaran',
                'label' => 'Pengeluaran',
                'target' => 20000000,
                'icon' => 'fa-solid fa-receipt',
            ],
            'laba' => [
                'key' => 'laba',
                'label' => 'Laba',
                'target' => 15000000,
                'icon' => 'fa-solid fa-wallet',
            ],
        ];
    }

    protected function loadMatrixTargets(): array
    {
        $default = $this->defaultMatrixTargets();

        if (!Storage::disk('local')->exists($this->matrixStorage)) {
            return $default;
        }

        $stored = json_decode(Storage::disk('local')->get($this->matrixStorage), true);

        if (!is_array($stored)) {
            return $default;
        }

        return $this->normalizeMatrixTargets(array_merge($default, $stored));
    }

    protected function normalizeMatrixTargets(array $targets): array
    {
        $defaults = $this->defaultMatrixTargets();

        return collect($defaults)
            ->map(function ($default, $key) use ($targets) {
                $input = $targets[$key] ?? null;

                if (is_array($input)) {
                    $targetValue = $input['target'] ?? $default['target'];
                    $label = $input['label'] ?? $default['label'];
                    $icon = $input['icon'] ?? $default['icon'];
                } else {
                    $targetValue = $input ?? $default['target'];
                    $label = $default['label'];
                    $icon = $default['icon'];
                }

                return [
                    'key' => $key,
                    'label' => $label,
                    'target' => max((float) $targetValue, 0),
                    'icon' => $icon,
                ];
            })
            ->toArray();
    }

    protected function calculateFinancialMetrics(): array
    {
        $pendapatan = $this->resolvePendapatanAggregate();
        $pengeluaran = $this->resolvePengeluaranAggregate();

        $laba = $pendapatan['total'] - $pengeluaran['total'];

        return [
            'pendapatan' => $pendapatan['total'],
            'pengeluaran' => $pengeluaran['total'],
            'laba' => $laba,
            '_breakdown' => [
                'pendapatan' => $pendapatan['breakdown'],
                'pengeluaran' => $pengeluaran['breakdown'],
            ],
        ];
    }

    protected function resolvePendapatanAggregate(): array
    {
        $pendapatanProduksi = (float) PencatatanProduksi::select(DB::raw('COALESCE(SUM(jumlah_produksi * COALESCE(harga_per_unit, 0)), 0) as total'))
            ->value('total');

        return [
            'total' => $pendapatanProduksi,
            'breakdown' => [
                'produksi' => $pendapatanProduksi,
            ],
        ];
    }

    protected function resolvePengeluaranAggregate(): array
    {
        $pengeluaranPembesaran = (float) Pakan::whereNotNull('batch_produksi_id')
            ->select(DB::raw('COALESCE(SUM(total_biaya), 0) as total'))
            ->value('total');

        $pengeluaranProduksi = (float) Pakan::whereNotNull('produksi_id')
            ->select(DB::raw('COALESCE(SUM(total_biaya), 0) as total'))
            ->value('total');

        $biayaKesehatan = (float) Kesehatan::select(DB::raw('COALESCE(SUM(biaya), 0) as total'))
            ->value('total');

        $totalPengeluaran = $pengeluaranPembesaran + $pengeluaranProduksi + $biayaKesehatan;

        return [
            'total' => $totalPengeluaran,
            'breakdown' => [
                'pembesaran_feed' => $pengeluaranPembesaran,
                'produksi_feed' => $pengeluaranProduksi,
                'kesehatan' => $biayaKesehatan,
            ],
        ];
    }

    protected function buildMatrixSnapshot(array $targets, array $metrics): array
    {
        return collect($targets)
            ->map(function ($config, $key) use ($metrics) {
                $actual = (float) ($metrics[$key] ?? 0);
                $target = max((float) ($config['target'] ?? 1), 1);
                $percent = round(($actual / $target) * 100, 1);
                $trend = $this->determineMatrixTrend($key, $actual, $target);

                return array_merge($config, [
                    'actual' => $actual,
                    'percent' => $percent,
                    'trend' => $trend,
                    'comparison' => $this->compareToTarget($actual, $target),
                ]);
            })
            ->toArray();
    }

    protected function compareToTarget(float $actual, float $target): string
    {
        if ($target <= 0) {
            return 'equal';
        }

        $difference = $actual - $target;

        if (abs($difference) <= $this->matrixEqualityTolerance) {
            return 'equal';
        }

        return $difference > 0 ? 'above' : 'below';
    }

    protected function appendGoalsMatrixCard(array $snapshot): array
    {
        $goals = $this->getDashboardGoals();
        $totalGoals = count($goals);

        $completedGoals = collect($goals)
            ->filter(function ($goal) {
                $current = (int) ($goal['current'] ?? 0);
                $target = (int) ($goal['target'] ?? 0);

                return $target > 0 && $current >= $target;
            })
            ->count();

        $goalTarget = $totalGoals;
        $percent = $goalTarget > 0
            ? round(($completedGoals / max($goalTarget, 1)) * 100, 1)
            : 0;

        $comparison = $this->compareGoalsProgress($completedGoals, $goalTarget);
        $trend = $this->determineGoalsTrend($completedGoals, $goalTarget);

        $snapshot['goals'] = [
            'key' => 'goals',
            'label' => 'Goals',
            'target' => $goalTarget,
            'actual' => $completedGoals,
            'percent' => $percent,
            'trend' => $trend,
            'icon' => 'fa-solid fa-bullseye',
            'comparison' => $comparison,
        ];

        return $snapshot;
    }

    protected function determineMatrixTrend(string $key, float $actual, float $target): string
    {
        if ($target <= 0) {
            return 'left';
        }

        if (abs($actual - $target) <= $this->matrixEqualityTolerance) {
            return 'left';
        }

        $achievement = $actual / $target;
        $isLowerBetter = $key === 'pengeluaran';

        if ($isLowerBetter) {
            if ($actual <= $target) {
                return 'up';
            }

            if ($actual <= $target * 1.15) {
                return 'left';
            }

            return 'down';
        }

        if ($achievement >= 1) {
            return 'up';
        }

        if ($achievement >= 0.7) {
            return 'left';
        }

        return 'down';
    }

    protected function compareGoalsProgress(int $completedGoals, int $goalTarget): string
    {
        if ($goalTarget <= 0) {
            return $completedGoals > 0 ? 'above' : 'equal';
        }

        if ($completedGoals > $goalTarget) {
            return 'above';
        }

        if ($completedGoals < $goalTarget) {
            return 'below';
        }

        return 'equal';
    }

    protected function determineGoalsTrend(int $completedGoals, int $goalTarget): string
    {
        if ($goalTarget <= 0) {
            return $completedGoals > 0 ? 'up' : 'left';
        }

        if ($completedGoals < $goalTarget) {
            return 'down';
        }

        if ($completedGoals === $goalTarget) {
            return 'left';
        }

        return 'up';
    }
}