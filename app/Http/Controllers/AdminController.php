<?php

namespace App\Http\Controllers;

use App\Models\MonitoringLingkungan;
use App\Models\Penetasan;
use App\Models\Pembesaran;
use App\Models\Produksi;
use App\Models\Kandang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

/**
 * ==========================================
 * Controller : AdminController
 * Deskripsi  : Mengelola dashboard admin serta ringkasan modul produksi, penetasan, dan pembesaran.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class AdminController extends Controller
{
    protected array $activityDateColumns = [
        Produksi::class => ['tanggal_mulai', 'tanggal', Produksi::CREATED_AT],
        Penetasan::class => ['tanggal_menetas', 'tanggal_simpan_telur', Penetasan::CREATED_AT],
        Pembesaran::class => ['tanggal_masuk', 'tanggal_siap', Pembesaran::CREATED_AT],
    ];

    public function dashboard()
    {
        /**
         * Menampilkan dashboard admin dengan metrik, goals, dan konfigurasi performa.
         * Menggabungkan snapshot matriks, tujuan dashboard, dan dataset aktivitas.
         */
        // Muat tujuan dashboard dari SistemController
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
        /**
         * Menampilkan halaman daftar kandang (view index kandang).
         */
        return view('admin.pages.kandang.index-kandang');
    }

    public function karyawan()
    {
        /**
         * Menampilkan halaman daftar karyawan untuk manajemen pengguna internal.
         */
        return view('admin.pages.karyawan.index-karyawan');
    }

    public function pembesaran()
    {
        /**
         * Menampilkan halaman daftar pembesaran (batch pembesaran) untuk admin.
         */
        return view('admin.pages.pembesaran.index-pembesaran');
    }

    public function penetasan(Request $request)
    {
        /**
         * Menampilkan halaman penetasan dengan pencarian dan snapshot IoT bila diaktifkan.
         * Menerima parameter paginasi dan pencarian melalui query string.
         */
        $perPage = $request->get('per_page', 5);
        $search = $request->get('search', '');

        $query = Penetasan::with('kandang', 'creator', 'updater');

        // Fungsionalitas pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('jumlah_telur', 'like', "%{$search}%")
                    ->orWhere('jumlah_menetas', 'like', "%{$search}%")
                    ->orWhere('jumlah_doc', 'like', "%{$search}%")
                    ->orWhere('tanggal_simpan_telur', 'like', "%{$search}%")
                    ->orWhere('tanggal_menetas', 'like', "%{$search}%")
                    ->orWhereHas('kandang', function ($q) use ($search) {
                        $q->where('nama_kandang', 'like', "%{$search}%");
                    });
            });
        }

        // Tangani opsi "Semua"
        if ($perPage === 'all') {
            $penetasan = $query->orderBy('dibuat_pada', 'desc')->get();
            // Buat paginator tiruan untuk semua record
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

        $sistemController = app(SistemController::class);
        $iotSettings = $sistemController->getIotSettings();
        $iotMode = $iotSettings['mode'] ?? 'simple';
        $iotSnapshots = [];

        if ($iotMode === 'iot') {
            $penetasanItems = collect($penetasan->items());
            $kandangIds = $penetasanItems
                ->pluck('kandang_id')
                ->filter(fn($id) => $id !== null)
                ->unique();

            if ($kandangIds->isNotEmpty()) {
                $iotSnapshots = MonitoringLingkungan::whereIn('kandang_id', $kandangIds)
                    ->orderByDesc('waktu_pencatatan')
                    ->get()
                    ->unique('kandang_id')
                    ->mapWithKeys(function ($record) {
                        $recordedAt = $record->waktu_pencatatan instanceof Carbon
                            ? $record->waktu_pencatatan
                            : ($record->waktu_pencatatan ? Carbon::parse($record->waktu_pencatatan) : null);

                        return [
                            $record->kandang_id => [
                                'suhu' => $record->suhu,
                                'kelembaban' => $record->kelembaban,
                                'waktu' => $recordedAt ? $recordedAt->format('d/m/Y H:i') : null,
                            ],
                        ];
                    })
                    ->toArray();
            }
        }
        $currentUser = Auth::user();
        $hatcherKandangOptions = Kandang::query()
            ->whereIn('status', ['aktif', 'active', 'berjalan', 'proses'])
            ->where(function ($query) {
                $query->whereRaw("LOWER(COALESCE(tipe_kandang, '')) LIKE ?", ['%hatch%'])
                    ->orWhereRaw("LOWER(COALESCE(tipe_kandang, '')) LIKE ?", ['%penetasan%'])
                    ->orWhereNull('tipe_kandang');
            })
            ->orderBy('nama_kandang')
            ->get()
            ->map(fn ($kandang) => [
                'id' => $kandang->id,
                'label' => $kandang->nama_dengan_detail,
                'tipe' => $kandang->tipe_kandang,
            ])
            ->values();

        return view('admin.pages.penetasan.index-penetasan', compact('penetasan', 'iotMode', 'iotSnapshots', 'currentUser', 'hatcherKandangOptions'));
    }

    public function produksi(Request $request)
    {
        /**
         * Menampilkan halaman produksi dengan metrik KPI, filter, dan ringkasan batch.
         * Mengumpulkan statistik seperti total telur, rata-rata telur per hari, dan loss rate.
         */
        // Paginasi dan filter dasar
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = \App\Models\Produksi::with('kandang');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('batch_produksi_id', 'like', "%{$search}%")
                    ->orWhere('catatan', 'like', "%{$search}%")
                    ->orWhere('tipe_produksi', 'like', "%{$search}%")
                    ->orWhereHas('kandang', function ($q) use ($search) {
                        $q->where('nama_kandang', 'like', "%{$search}%");
                    });
            });
        }

        $produksi = $perPage === 'all'
            ? $query->orderBy('tanggal_mulai', 'desc')->get()
            : $query->orderBy('tanggal_mulai', 'desc')->paginate($perPage);

        // Agregat KPI
        $totalTelur = \App\Models\LaporanHarian::selectRaw('COALESCE(SUM(produksi_telur),0) as total')->value('total') ?? 0;

        // Rata-rata telur per hari dari laporan harian yang memiliki produksi
        $laporanCount = \App\Models\LaporanHarian::where('produksi_telur', '>', 0)->count();
        $rataTelurPerHari = $laporanCount > 0 ? round($totalTelur / $laporanCount, 2) : 0;

        // Pendapatan estimasi
        $pendapatan = $produksi->sum(function ($p) {
            return ($p->jumlah_telur ?? 0) * ($p->harga_per_pcs ?? 0);
        });

        // Tingkat kehilangan: hitung dari mortalitas kumulatif rata-rata
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

    // Placeholder minimal untuk membuat produksi
    public function produksiCreate()
    {
        // Untuk saat ini, tampilkan halaman placeholder pembuatan sederhana atau redirect kembali
        return view('admin.pages.produksi.create-produksi');
    }

    // Placeholder minimal untuk menampilkan record produksi
    public function produksiShow($id)
    {
        return redirect()->route('admin.produksi.show', $id);
    }

    // Placeholder minimal untuk mengedit record produksi
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
            ->map(fn($month) => Carbon::create($year, $month, 1)->translatedFormat('M'))
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
        $labels = array_map(fn($year) => (string) $year, $years);

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
            ->map(fn($month) => (int) ($raw[$month] ?? 0))
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
            ->map(fn($year) => (int) ($raw[$year] ?? 0))
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
            ->map(fn($date) => (int) ($raw[$date] ?? 0))
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

        return array_values(array_filter($columns, fn($column) => Schema::hasColumn($table, $column)));
    }
}
