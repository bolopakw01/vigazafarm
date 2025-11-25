<?php

namespace App\Http\Controllers;

use App\Models\Penetasan;
use App\Models\Pembesaran;
use App\Models\Produksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    protected array $activityDateColumns = [
        Produksi::class => ['tanggal_mulai', 'tanggal', Produksi::CREATED_AT],
        Penetasan::class => ['tanggal_menetas', 'tanggal_simpan_telur', Penetasan::CREATED_AT],
        Pembesaran::class => ['tanggal_masuk', 'tanggal_siap', Pembesaran::CREATED_AT],
    ];

    public function dashboard()
    {
        // Load dashboard goals from SistemController
        $sistemController = app(SistemController::class);
        $goals = $sistemController->getDashboardGoals();
        $matrixCards = $sistemController->getMatrixSnapshot();
        $matrixEnabled = $sistemController->isMatrixEnabled();
        $activityDatasets = $this->prepareActivityDatasets();
        $performanceChart = $sistemController->getPerformanceChartConfig();

        return view('admin.dashboard-admin', compact('goals', 'matrixCards', 'matrixEnabled', 'activityDatasets', 'performanceChart'));
    }

    public function kandang()
    {
        return view('admin.pages.kandang.index-kandang');
    }

    public function karyawan()
    {
        return view('admin.pages.karyawan.index-karyawan');
    }

    public function pembesaran()
    {
        return view('admin.pages.pembesaran.index-pembesaran');
    }

    public function penetasan(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $search = $request->get('search', '');

        $query = Penetasan::with('kandang');

        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('jumlah_telur', 'like', "%{$search}%")
                  ->orWhere('jumlah_menetas', 'like', "%{$search}%")
                  ->orWhere('jumlah_doc', 'like', "%{$search}%")
                  ->orWhere('tanggal_simpan_telur', 'like', "%{$search}%")
                  ->orWhere('tanggal_menetas', 'like', "%{$search}%")
                  ->orWhereHas('kandang', function($q) use ($search) {
                      $q->where('nama_kandang', 'like', "%{$search}%");
                  });
            });
        }

        // Handle "Semua" option
        if ($perPage === 'all') {
            $penetasan = $query->orderBy('dibuat_pada', 'desc')->get();
            // Create a mock paginator for "all" records
            $penetasan = new \Illuminate\Pagination\LengthAwarePaginator(
                $penetasan,
                $penetasan->count(),
                $penetasan->count(),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $penetasan = $query->orderBy('dibuat_pada', 'desc')->paginate($perPage);
        }
        
        return view('admin.pages.penetasan.index-penetasan', compact('penetasan'));
    }

    public function produksi(Request $request)
    {
        // Basic pagination and filters
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = \App\Models\Produksi::with('kandang');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('batch_produksi_id', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhere('tipe_produksi', 'like', "%{$search}%")
                  ->orWhereHas('kandang', function($q) use ($search) {
                      $q->where('nama_kandang', 'like', "%{$search}%");
                  });
            });
        }

        $produksi = $perPage === 'all'
            ? $query->orderBy('tanggal_mulai', 'desc')->get()
            : $query->orderBy('tanggal_mulai', 'desc')->paginate($perPage);

        // KPI aggregates
        $totalTelur = \App\Models\LaporanHarian::selectRaw('COALESCE(SUM(produksi_telur),0) as total')->value('total') ?? 0;
        
        // Rata-rata telur per hari dari laporan harian yang memiliki produksi
        $laporanCount = \App\Models\LaporanHarian::where('produksi_telur', '>', 0)->count();
        $rataTelurPerHari = $laporanCount > 0 ? round($totalTelur / $laporanCount, 2) : 0;
        
        // Pendapatan estimasi
        $pendapatan = $produksi->sum(function($p) { 
            return ($p->jumlah_telur ?? 0) * ($p->harga_per_pcs ?? 0); 
        });
        
        // Loss rate: hitung dari mortalitas kumulatif rata-rata
        // Atau bisa dari jumlah kematian dibanding populasi
        $totalKematian = \App\Models\LaporanHarian::selectRaw('COALESCE(SUM(jumlah_kematian),0) as total')->value('total') ?? 0;
        $totalPopulasi = \App\Models\LaporanHarian::selectRaw('COALESCE(AVG(jumlah_burung),0) as total')->value('total') ?? 0;
        $lostRate = $totalPopulasi > 0 ? round(($totalKematian / $totalPopulasi) * 100, 2) : 0;
        
        // Batch aktif (status aktif)
        $batchAktif = \App\Models\Produksi::where('status', 'aktif')->distinct('batch_produksi_id')->count('batch_produksi_id');
        
        // Kandang aktif yang sedang produksi
        $kandangAktif = \App\Models\Produksi::where('status', 'aktif')->distinct('kandang_id')->count('kandang_id');
        
        // Usia rata-rata produksi (dalam hari dari tanggal_mulai)
        $usiaRataRata = \App\Models\Produksi::where('status', 'aktif')
            ->selectRaw('AVG(DATEDIFF(NOW(), tanggal_mulai)) as avg_usia')
            ->value('avg_usia') ?? 0;
        $usiaRataRata = round($usiaRataRata);
        
        // Total indukan dari semua batch aktif
        $totalIndukan = \App\Models\Produksi::where('status', 'aktif')->sum('jumlah_indukan') ?? 0;

        $kandangList = \App\Models\Kandang::orderBy('nama_kandang')->get();

        return view('admin.pages.produksi.index-produksi', compact(
            'produksi', 
            'kandangList', 
            'totalTelur', 
            'rataTelurPerHari', 
            'pendapatan', 
            'lostRate',
            'batchAktif',
            'kandangAktif',
            'usiaRataRata',
            'totalIndukan'
        ));
    }

    // Minimal placeholder for creating produksi
    public function produksiCreate()
    {
        // For now, show a simple create placeholder page or redirect back
        return view('admin.pages.produksi.create-produksi');
    }

    // Minimal placeholder for showing a produksi record
    public function produksiShow($id)
    {
        return redirect()->route('admin.produksi.show', $id);
    }

    // Minimal placeholder for editing a produksi record
    public function produksiEdit($id)
    {
        return view('admin.pages.produksi.edit-produksi', ['id' => $id]);
    }

    protected function prepareActivityDatasets(): array
    {
        return [
            'bulan' => $this->buildMonthlySeries(),
            'tahun' => $this->buildYearlySeries(),
            'hari' => $this->buildDailySeries(),
        ];
    }

    protected function buildMonthlySeries(): array
    {
        $year = now()->year;
        $labels = collect(range(1, 12))
            ->map(fn ($month) => Carbon::create($year, $month, 1)->translatedFormat('M'))
            ->toArray();

        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Produksi',
                    'type' => 'column',
                    'data' => $this->hydrateMonthlyData(Produksi::class, $year),
                ],
                [
                    'name' => 'Penetasan',
                    'type' => 'area',
                    'data' => $this->hydrateMonthlyData(Penetasan::class, $year),
                ],
                [
                    'name' => 'Pembesaran',
                    'type' => 'line',
                    'data' => $this->hydrateMonthlyData(Pembesaran::class, $year),
                ],
            ],
        ];
    }

    protected function buildYearlySeries(): array
    {
        $endYear = now()->year;
        $years = range($endYear - 4, $endYear);
        $labels = array_map(fn ($year) => (string) $year, $years);

        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Produksi',
                    'type' => 'column',
                    'data' => $this->hydrateYearlyData(Produksi::class, $years),
                ],
                [
                    'name' => 'Penetasan',
                    'type' => 'area',
                    'data' => $this->hydrateYearlyData(Penetasan::class, $years),
                ],
                [
                    'name' => 'Pembesaran',
                    'type' => 'line',
                    'data' => $this->hydrateYearlyData(Pembesaran::class, $years),
                ],
            ],
        ];
    }

    protected function buildDailySeries(): array
    {
        $end = now();
        $start = (clone $end)->subDays(6);
        $dateKeys = [];
        $labels = [];

        for ($i = 0; $i < 7; $i++) {
            $day = (clone $start)->addDays($i);
            $dateKeys[] = $day->toDateString();
            $labels[] = $day->translatedFormat('d M');
        }

        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Produksi',
                    'type' => 'column',
                    'data' => $this->hydrateDailyData(Produksi::class, $start, $end, $dateKeys),
                ],
                [
                    'name' => 'Penetasan',
                    'type' => 'area',
                    'data' => $this->hydrateDailyData(Penetasan::class, $start, $end, $dateKeys),
                ],
                [
                    'name' => 'Pembesaran',
                    'type' => 'line',
                    'data' => $this->hydrateDailyData(Pembesaran::class, $start, $end, $dateKeys),
                ],
            ],
        ];
    }

    protected function hydrateMonthlyData(string $modelClass, int $year): array
    {
        $dateExpression = $this->resolveActivityDateExpression($modelClass);
        $raw = $modelClass::query()
            ->selectRaw('MONTH(' . $dateExpression . ') as month_key, COUNT(*) as total')
            ->whereRaw($dateExpression . ' IS NOT NULL')
            ->whereRaw('YEAR(' . $dateExpression . ') = ?', [$year])
            ->groupByRaw('MONTH(' . $dateExpression . ')')
            ->pluck('total', 'month_key')
            ->all();

        return collect(range(1, 12))
            ->map(fn ($month) => (int) ($raw[$month] ?? 0))
            ->toArray();
    }

    protected function hydrateYearlyData(string $modelClass, array $years): array
    {
        if (empty($years)) {
            return [];
        }

        $dateExpression = $this->resolveActivityDateExpression($modelClass);
        $start = Carbon::create(min($years), 1, 1)->startOfYear();
        $end = Carbon::create(max($years), 12, 31)->endOfYear();

        $raw = $modelClass::query()
            ->selectRaw('YEAR(' . $dateExpression . ') as year_key, COUNT(*) as total')
            ->whereRaw($dateExpression . ' IS NOT NULL')
            ->whereRaw($dateExpression . ' BETWEEN ? AND ?', [$start, $end])
            ->groupByRaw('YEAR(' . $dateExpression . ')')
            ->pluck('total', 'year_key')
            ->all();

        return collect($years)
            ->map(fn ($year) => (int) ($raw[$year] ?? 0))
            ->toArray();
    }

    protected function hydrateDailyData(string $modelClass, Carbon $start, Carbon $end, array $dateKeys): array
    {
        $dateExpression = $this->resolveActivityDateExpression($modelClass);

        $raw = $modelClass::query()
            ->selectRaw('DATE(' . $dateExpression . ') as date_key, COUNT(*) as total')
            ->whereRaw($dateExpression . ' IS NOT NULL')
            ->whereRaw($dateExpression . ' BETWEEN ? AND ?', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->groupByRaw('DATE(' . $dateExpression . ')')
            ->pluck('total', 'date_key')
            ->all();

        return collect($dateKeys)
            ->map(fn ($date) => (int) ($raw[$date] ?? 0))
            ->toArray();
    }

    protected function resolveCreatedColumn(string $modelClass): string
    {
        if (defined($modelClass . '::CREATED_AT') && $modelClass::CREATED_AT) {
            return $modelClass::CREATED_AT;
        }

        $model = new $modelClass();

        return $model->getCreatedAtColumn();
    }

    protected function resolveActivityDateExpression(string $modelClass): string
    {
        $columns = $this->resolveExistingDateColumns($modelClass);

        if (empty($columns)) {
            return $this->resolveCreatedColumn($modelClass);
        }

        return count($columns) === 1
            ? $columns[0]
            : 'COALESCE(' . implode(', ', $columns) . ')';
    }

    protected function resolveExistingDateColumns(string $modelClass): array
    {
        $model = new $modelClass();
        $table = $model->getTable();

        $columns = array_filter(array_map('trim', $this->activityDateColumns[$modelClass] ?? []));
        $columns[] = $this->resolveCreatedColumn($modelClass);
        $columns = array_unique($columns);

        if (!Schema::hasTable($table)) {
            return $columns;
        }

        return array_values(array_filter($columns, fn ($column) => Schema::hasColumn($table, $column)));
    }
}
