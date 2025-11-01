<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penetasan;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard-admin');
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
}
