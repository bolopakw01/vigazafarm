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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\Dss\DssSettingsService;

/**
 * ==========================================
 * Controller : SistemController
 * Deskripsi  : Mengatur konfigurasi sistem seperti dashboard, matriks finansial, IoT, dan pengaturan performa.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class SistemController extends Controller
{
    protected string $goalsStorage = 'dashboard_goals.json';
    protected string $matrixStorage = 'dashboard_matrix.json';
    protected string $performanceStorage = 'dashboard_performance.json';
    protected string $iotSettingsStorage = 'iot_settings.json';
    protected float $matrixEqualityTolerance = 500;
    protected DssSettingsService $dssSettings;

    public function __construct(DssSettingsService $dssSettings)
    {
        $this->dssSettings = $dssSettings;
    }

    public function index()
    {
        /**
         * Menampilkan halaman setelan sistem utama.
         */
        return view('admin.pages.sistem.index');
    }

    public function iot()
    {
        /**
         * Menampilkan pengaturan plugin IoT (suhu & kelembaban) untuk konfigurasi.
         */
        $settings = $this->loadIotSettings();

        return view('admin.pages.sistem.plugin.suhudankelembapan', compact('settings'));
    }

    public function updateIot(Request $request)
    {
        /**
         * Memproses pembaruan konfigurasi IoT sesuai input, menyimpan ke storage.
         */
        $mode = $request->input('mode', 'simple');

        $rules = [
            'mode' => ['required', Rule::in(['simple', 'iot'])],
            'api_endpoint' => [$mode === 'iot' ? 'required' : 'nullable', 'url'],
            'api_key' => [$mode === 'iot' ? 'required' : 'nullable', 'string', 'max:255'],
            'device_id' => [$mode === 'iot' ? 'required' : 'nullable', 'string', 'max:100'],
            'update_interval' => [$mode === 'iot' ? 'required' : 'nullable', 'integer', Rule::in([30, 60, 300, 600])],
        ];

        $validated = Validator::make($request->all(), $rules)->validate();

        $settings = $this->normalizeIotSettings($validated);

        Storage::disk('local')->put(
            $this->iotSettingsStorage,
            json_encode($settings, JSON_PRETTY_PRINT)
        );

        return redirect()
            ->route('admin.sistem.iot')
            ->with('success', 'Pengaturan IoT berhasil diperbarui.');
    }

    public function dss()
    {
        $settings = $this->dssSettings->getSettings();

        return view('admin.pages.sistem.plugin.dss', [
            'settings' => $settings,
            'mode' => $settings['mode'] ?? 'config',
            'configSettings' => $settings['config'] ?? [],
            'mlSettings' => $settings['ml'] ?? [],
        ]);
    }

    public function updateDss(Request $request)
    {
        $validated = $request->validate([
            'mode' => ['required', Rule::in(['config', 'ml'])],
            'config.eggs.max_batches' => ['required', 'integer', 'min:1', 'max:10'],
            'config.eggs.hatcher_warning_days' => ['required', 'integer', 'min:0', 'max:14'],
            'config.eggs.hatcher_critical_days' => ['required', 'integer', 'min:0', 'max:14'],
            'config.eggs.hatch_rate_warning' => ['required', 'numeric', 'min:0', 'max:100'],
            'config.eggs.hatch_rate_critical' => ['required', 'numeric', 'min:0', 'max:100'],
            'config.feed.max_insights' => ['required', 'integer', 'min:1', 'max:20'],
            'config.feed.history_days' => ['required', 'integer', 'min:1', 'max:30'],
            'config.feed.warning_ratio' => ['required', 'numeric', 'min:0', 'max:1'],
            'config.feed.critical_ratio' => ['required', 'numeric', 'min:0', 'max:1'],
            'config.mortality.window_days' => ['required', 'integer', 'min:1', 'max:14'],
            'config.mortality.max_items' => ['required', 'integer', 'min:1', 'max:10'],
            'config.mortality.warning_pct' => ['required', 'numeric', 'min:0', 'max:100'],
            'config.mortality.critical_pct' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $this->dssSettings->save($validated);

        return redirect()
            ->route('admin.sistem.dss')
            ->with('success', 'Pengaturan DSS berhasil disimpan.');
    }

    public function dashboard()
    {
        /**
         * Menampilkan halaman konfigurasi tujuan dashboard (set goals).
         */
        $goals = $this->loadDashboardGoals();

        return view('admin.pages.sistem.dashboard.setgoals', compact('goals'));
    }

    public function updateDashboard(Request $request)
    {
        /**
         * Memperbarui konfigurasi target dashboard dan menyimpannya.
         */
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
        /**
         * Menampilkan konfigurasi matriks finansial dan snapshot perhitungan.
         */
        $matrixData = $this->loadMatrixData();
        $hasCustomTargets = $this->hasStoredMatrixTargets();
        $targets = $matrixData['targets'];
        $matriks_enabled = $hasCustomTargets ? (bool) ($matrixData['enabled'] ?? true) : false;
        $metrics = $this->calculateFinancialMetrics();
        $snapshot = $this->buildMatrixSnapshot($targets, $metrics);

        return view('admin.pages.sistem.dashboard.setmatriks', compact('targets', 'metrics', 'snapshot', 'matriks_enabled'));
    }

    public function updateMatrix(Request $request)
    {
        /**
         * Memperbarui target matriks finansial dan menyimpannya ke storage.
         */
        $validated = $request->validate([
            'targets' => 'required|array',
            'targets.pendapatan' => 'required|numeric|min:0',
            'targets.pengeluaran' => 'required|numeric|min:0',
            'targets.laba' => 'required|numeric|min:0',
            'matriks_enabled' => 'nullable|boolean',
        ]);

        $targets = $this->normalizeMatrixTargets($validated['targets']);
        $enabled = $request->boolean('matriks_enabled');

        $data = [
            'targets' => $targets,
            'enabled' => $enabled,
        ];

        Storage::disk('local')->put(
            $this->matrixStorage,
            json_encode($data, JSON_PRETTY_PRINT)
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

    public function performance()
    {
        /**
         * Menampilkan halaman konfigurasi grafik performance.
         */
        $performance = $this->loadPerformanceSettings();

        return view('admin.pages.sistem.dashboard.setperformance', compact('performance'));
    }

    public function updatePerformance(Request $request)
    {
        /**
         * Memperbarui pengaturan grafik performance dan menyimpannya.
         */
        $validated = $request->validate([
            'series' => 'required|array|min:1|max:4',
            'series.*.key' => 'nullable|string|max:60',
            'series.*.label' => 'required|string|max:50',
            'series.*.color' => ['required', 'regex:/^#([0-9a-fA-F]{3}){1,2}$/'],
            'categories' => 'nullable|array|max:8',
            'categories.*.label' => 'required|string|max:60',
            'categories.*.values' => 'required|array',
            'categories.*.values.*' => 'nullable|numeric|min:0|max:200',
        ]);

        $series = collect($validated['series'])
            ->map(function ($item) {
                $label = trim($item['label']);
                $rawKey = $item['key'] ?? $label;
                $key = Str::slug($rawKey ?: $label ?: Str::random(4), '_');
                if (!$key) {
                    $key = Str::slug(Str::random(8), '_');
                }
                return [
                    'key' => $key,
                    'label' => $label ?: 'Series',
                    'color' => $item['color'],
                ];
            })
            ->unique('key')
            ->values();

        $seriesKeys = $series->pluck('key');

        $categories = collect($validated['categories'] ?? [])
            ->map(function ($item) use ($seriesKeys) {
                $values = collect($item['values'] ?? [])
                    ->map(fn ($value) => (float) $value)
                    ->all();

                $normalized = $seriesKeys->mapWithKeys(function ($key) use ($values) {
                    return [$key => (float) ($values[$key] ?? 0)];
                });

                return [
                    'label' => trim($item['label']) ?: 'Kategori',
                    'values' => $normalized->all(),
                ];
            })
            ->values();

        $payload = [
            'series' => $series->all(),
            'categories' => $categories->all(),
        ];

        Storage::disk('local')->put(
            $this->performanceStorage,
            json_encode($payload, JSON_PRETTY_PRINT)
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi grafik performance berhasil diperbarui!'
            ]);
        }

        return redirect()->route('admin.sistem.performance')
            ->with('success', 'Konfigurasi grafik performance berhasil diperbarui!');
    }

    public function getDashboardGoals()
    {
        return $this->applyLiveMetrics($this->loadDashboardGoals());
    }

    public function getIotSettings(): array
    {
        return $this->loadIotSettings();
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
                'unit' => 'doq',
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
        $matrixData = $this->loadMatrixData();

        if (!$matrixData['enabled'] || !$this->hasStoredMatrixTargets()) {
            return [];
        }

        $targets = $matrixData['targets'];
        $metrics = $this->calculateFinancialMetrics();

        $snapshot = $this->buildMatrixSnapshot($targets, $metrics);

        return $this->appendGoalsMatrixCard($snapshot);
    }

    public function isMatrixEnabled(): bool
    {
        return $this->hasStoredMatrixTargets() && $this->loadMatrixData()['enabled'];
    }

    public function getPerformanceChartConfig(): array
    {
        $settings = $this->loadPerformanceSettings();
        $series = $settings['series'] ?? [];
        $categories = $settings['categories'] ?? [];

        $labels = collect($categories)->pluck('label')->map(fn ($label) => $label ?: 'Kategori')->values()->all();

        $seriesData = collect($series)->map(function ($serie) use ($categories) {
            $key = $serie['key'];
            $data = collect($categories)
                ->map(fn ($category) => (float) ($category['values'][$key] ?? 0))
                ->values()
                ->all();

            return [
                'name' => $serie['label'],
                'data' => $data,
            ];
        })->values()->all();

        $colors = collect($series)->pluck('color')->values()->all();

        return [
            'labels' => $labels,
            'series' => $seriesData,
            'colors' => $colors,
        ];
    }

    protected function loadPerformanceSettings(): array
    {
        if (!Storage::disk('local')->exists($this->performanceStorage)) {
            return $this->defaultPerformanceSettings();
        }

        $stored = json_decode(Storage::disk('local')->get($this->performanceStorage), true);

        return is_array($stored)
            ? $this->normalizePerformanceSettings($stored)
            : $this->defaultPerformanceSettings();
    }

    protected function defaultPerformanceSettings(): array
    {
        return [
            'series' => [
                ['key' => 'produksi', 'label' => 'Produksi', 'color' => '#198754'],
                ['key' => 'pendapatan', 'label' => 'Pendapatan', 'color' => '#0d6efd'],
                ['key' => 'pengeluaran', 'label' => 'Pengeluaran', 'color' => '#ffc107'],
            ],
            'categories' => [
                ['label' => 'Kualitas', 'values' => ['produksi' => 80, 'pendapatan' => 20, 'pengeluaran' => 44]],
                ['label' => 'Keuntungan', 'values' => ['produksi' => 50, 'pendapatan' => 30, 'pengeluaran' => 76]],
                ['label' => 'Efisiensi', 'values' => ['produksi' => 30, 'pendapatan' => 40, 'pengeluaran' => 78]],
                ['label' => 'Pertumbuhan', 'values' => ['produksi' => 40, 'pendapatan' => 80, 'pengeluaran' => 13]],
                ['label' => 'Stabilitas', 'values' => ['produksi' => 100, 'pendapatan' => 20, 'pengeluaran' => 43]],
                ['label' => 'Resiko', 'values' => ['produksi' => 20, 'pendapatan' => 80, 'pengeluaran' => 10]],
            ],
        ];
    }

    protected function normalizePerformanceSettings(array $settings): array
    {
        $defaults = $this->defaultPerformanceSettings();
        $hasCustomSeries = array_key_exists('series', $settings);
        $seriesSource = $hasCustomSeries ? ($settings['series'] ?? []) : $defaults['series'];

        $series = collect($seriesSource)
            ->map(function ($serie) {
                $label = trim($serie['label'] ?? 'Series');
                return [
                    'key' => Str::slug($serie['key'] ?? $label, '_'),
                    'label' => $label,
                    'color' => $serie['color'] ?? '#0d6efd',
                ];
            })
            ->filter(fn ($item) => $item['key'])
            ->unique('key')
            ->values();

        if ($series->isEmpty()) {
            $series = collect($defaults['series']);
        }

        $seriesKeys = $series->pluck('key');

        $hasCustomCategories = array_key_exists('categories', $settings);
        $categoriesSource = $hasCustomCategories ? ($settings['categories'] ?? []) : $defaults['categories'];

        $categories = collect($categoriesSource)
            ->map(function ($category) use ($seriesKeys) {
                $values = collect($category['values'] ?? [])
                    ->map(fn ($value) => (float) $value)
                    ->all();

                $normalizedValues = $seriesKeys->mapWithKeys(function ($key) use ($values) {
                    return [$key => (float) ($values[$key] ?? 0)];
                });

                return [
                    'label' => trim($category['label'] ?? 'Kategori'),
                    'values' => $normalizedValues->all(),
                ];
            })
            ->filter(fn ($item) => $item['label'])
            ->values();

        if ($categories->isEmpty() && !$hasCustomCategories) {
            $categories = collect($defaults['categories']);
        }

        return [
            'series' => $series->all(),
            'categories' => $categories->all(),
        ];
    }

    protected function loadIotSettings(): array
    {
        if (!Storage::disk('local')->exists($this->iotSettingsStorage)) {
            return $this->defaultIotSettings();
        }

        $stored = json_decode(Storage::disk('local')->get($this->iotSettingsStorage), true);

        return is_array($stored)
            ? $this->normalizeIotSettings($stored)
            : $this->defaultIotSettings();
    }

    protected function defaultIotSettings(): array
    {
        return [
            'mode' => 'simple',
            'api_endpoint' => null,
            'api_key' => null,
            'device_id' => null,
            'update_interval' => 60,
        ];
    }

    protected function normalizeIotSettings(array $settings): array
    {
        $defaults = $this->defaultIotSettings();

        $rawMode = $settings['mode'] ?? $defaults['mode'];
        $mode = in_array($rawMode, ['simple', 'iot'], true)
            ? $rawMode
            : $defaults['mode'];

        $allowedIntervals = [30, 60, 300, 600];
        $interval = (int) ($settings['update_interval'] ?? $defaults['update_interval']);
        if (!in_array($interval, $allowedIntervals, true)) {
            $interval = $defaults['update_interval'];
        }

        return [
            'mode' => $mode,
            'api_endpoint' => $settings['api_endpoint'] ?? $defaults['api_endpoint'],
            'api_key' => $settings['api_key'] ?? $defaults['api_key'],
            'device_id' => $settings['device_id'] ?? $defaults['device_id'],
            'update_interval' => $interval,
        ];
    }

    protected function defaultMatrixTargets(): array
    {
        return [
            'pendapatan' => [
                'key' => 'pendapatan',
                'label' => 'Total Pendapatan',
                'target' => null,
                'icon' => 'fa-solid fa-coins',
            ],
            'pengeluaran' => [
                'key' => 'pengeluaran',
                'label' => 'Total Pengeluaran',
                'target' => null,
                'icon' => 'fa-solid fa-receipt',
            ],
            'laba' => [
                'key' => 'laba',
                'label' => 'Total Laba',
                'target' => null,
                'icon' => 'fa-solid fa-wallet',
            ],
        ];
    }

    protected function loadMatrixData(): array
    {
        $defaultTargets = $this->defaultMatrixTargets();
        $default = [
            'targets' => $defaultTargets,
            'enabled' => true,
        ];

        if (!Storage::disk('local')->exists($this->matrixStorage)) {
            return $default;
        }

        $stored = json_decode(Storage::disk('local')->get($this->matrixStorage), true);

        if (!is_array($stored)) {
            return $default;
        }

        $hasStructuredData = array_key_exists('targets', $stored) || array_key_exists('enabled', $stored);
        $targetsRaw = $hasStructuredData ? ($stored['targets'] ?? $defaultTargets) : $stored;
        $enabled = (bool) ($hasStructuredData ? ($stored['enabled'] ?? $default['enabled']) : $default['enabled']);

        return [
            'targets' => $this->normalizeMatrixTargets($targetsRaw),
            'enabled' => $enabled,
        ];
    }

    protected function hasStoredMatrixTargets(): bool
    {
        return Storage::disk('local')->exists($this->matrixStorage);
    }

    protected function loadMatrixTargets(): array
    {
        return $this->loadMatrixData()['targets'];
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
                    'target' => $this->normalizeMatrixTargetValue($targetValue),
                    'icon' => $icon,
                ];
            })
            ->toArray();
    }

    protected function normalizeMatrixTargetValue($value): ?float
    {
        if (!is_numeric($value)) {
            return null;
        }

        return max((float) $value, 0);
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
                $rawTarget = $config['target'] ?? null;
                $target = is_numeric($rawTarget) ? max((float) $rawTarget, 0) : 0;
                $percent = $target > 0 ? round(($actual / $target) * 100, 1) : 0;
                $trend = $this->determineMatrixTrend($key, $actual, $target);

                return array_merge($config, [
                    'actual' => $actual,
                    'percent' => $percent,
                    'trend' => $trend,
                    'comparison' => $this->compareToTarget($actual, $target),
                    'target_numeric' => $target,
                ]);
            })
            ->toArray();
    }

    protected function compareToTarget(float $actual, float $target): string
    {
        if ($target <= 0) {
            return $actual > 0 ? 'above' : 'equal';
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
            'label' => 'Penyelesaian Tujuan',
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