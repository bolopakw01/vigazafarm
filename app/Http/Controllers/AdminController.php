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

    public function produksi()
    {
        return view('admin.pages.produksi.index-produksi');
    }
}
