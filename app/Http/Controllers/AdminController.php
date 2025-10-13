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
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        $produksi = $perPage === 'all'
            ? $query->orderBy('tanggal_mulai', 'desc')->get()
            : $query->orderBy('tanggal_mulai', 'desc')->paginate($perPage);

        // KPI aggregates (simple implementations)
        $totalTelur = \App\Models\LaporanHarian::selectRaw('COALESCE(SUM(produksi_telur),0) as total')->value('total') ?? 0;
        $rataTelurPerHari = $produksi->count() ? round(($produksi->sum('jumlah_telur') / $produksi->count()), 2) : 0;
        $pendapatan = $produksi->sum(function($p) { return ($p->jumlah_telur ?? 0) * ($p->harga_per_pcs ?? 0); });
        $lostRate = 0; // Calculation depends on stok & kematian; leave 0 for now

        $kandangList = \App\Models\Kandang::orderBy('nama_kandang')->get();

        return view('admin.pages.produksi.index-produksi', compact('produksi', 'kandangList', 'totalTelur', 'rataTelurPerHari', 'pendapatan', 'lostRate'));
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
        // Attempt to load a produksi model if exists, otherwise show simple message
        return view('admin.pages.produksi.show-produksi', ['id' => $id]);
    }

    // Minimal placeholder for editing a produksi record
    public function produksiEdit($id)
    {
        return view('admin.pages.produksi.edit-produksi', ['id' => $id]);
    }
}
