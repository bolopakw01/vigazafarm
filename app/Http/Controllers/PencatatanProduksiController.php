<?php

namespace App\Http\Controllers;

use App\Models\PencatatanProduksi;
use App\Models\Produksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PencatatanProduksiController extends Controller
{
    /**
     * Get pencatatan data for a specific produksi (AJAX)
     */
    public function getData(Request $request, Produksi $produksi)
    {
        $pencatatan = $produksi->pencatatanProduksi()
            ->with('dibuatOleh')
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pencatatan->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal_formatted,
                    'jumlah_produksi' => $item->jumlah_produksi,
                    'kualitas' => ucfirst($item->kualitas),
                    'berat_rata_rata' => $item->berat_rata_rata,
                    'harga_per_unit' => $item->harga_per_unit,
                    'total_pendapatan' => $item->total_pendapatan,
                    'catatan' => $item->catatan,
                    'dibuat_oleh' => $item->dibuatOleh->nama ?? 'Unknown',
                    'dibuat_pada' => $item->dibuat_pada->format('d/m/Y H:i')
                ];
            })
        ]);
    }

    /**
     * Store a new pencatatan
     */
    public function store(Request $request, Produksi $produksi)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date|before_or_equal:today',
            'jumlah_produksi' => 'required|integer|min:1',
            'kualitas' => 'required|in:baik,sedang,buruk',
            'berat_rata_rata' => 'nullable|numeric|min:0',
            'harga_per_unit' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if pencatatan for this date already exists
        $existing = $produksi->pencatatanProduksi()
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Pencatatan untuk tanggal ini sudah ada'
            ], 422);
        }

        $pencatatan = $produksi->pencatatanProduksi()->create([
            'tanggal' => $request->tanggal,
            'jumlah_produksi' => $request->jumlah_produksi,
            'kualitas' => $request->kualitas,
            'berat_rata_rata' => $request->berat_rata_rata,
            'harga_per_unit' => $request->harga_per_unit,
            'catatan' => $request->catatan,
            'dibuat_oleh' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pencatatan berhasil ditambahkan',
            'data' => $pencatatan
        ]);
    }

    /**
     * Update pencatatan
     */
    public function update(Request $request, PencatatanProduksi $pencatatan)
    {
        // Check ownership
        if ($pencatatan->dibuat_oleh !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengedit pencatatan ini'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date|before_or_equal:today',
            'jumlah_produksi' => 'required|integer|min:1',
            'kualitas' => 'required|in:baik,sedang,buruk',
            'berat_rata_rata' => 'nullable|numeric|min:0',
            'harga_per_unit' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if another pencatatan for this date exists (excluding current)
        $existing = $pencatatan->produksi->pencatatanProduksi()
            ->where('tanggal', $request->tanggal)
            ->where('id', '!=', $pencatatan->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Pencatatan untuk tanggal ini sudah ada'
            ], 422);
        }

        $pencatatan->update($request->only([
            'tanggal', 'jumlah_produksi', 'kualitas', 'berat_rata_rata', 'harga_per_unit', 'catatan'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Pencatatan berhasil diperbarui',
            'data' => $pencatatan
        ]);
    }

    /**
     * Delete pencatatan
     */
    public function destroy(PencatatanProduksi $pencatatan)
    {
        // Check ownership
        if ($pencatatan->dibuat_oleh !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus pencatatan ini'
            ], 403);
        }

        $pencatatan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pencatatan berhasil dihapus'
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics(Request $request, Produksi $produksi)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $pencatatan = $produksi->pencatatanProduksi()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $stats = [
            'total_produksi' => $pencatatan->sum('jumlah_produksi'),
            'rata_rata_harian' => $pencatatan->avg('jumlah_produksi') ?? 0,
            'total_pendapatan' => $pencatatan->sum('total_pendapatan'),
            'rata_rata_berat' => $pencatatan->avg('berat_rata_rata') ?? 0,
            'hari_terproduktif' => $pencatatan->sortByDesc('jumlah_produksi')->first()?->tanggal?->format('d/m/Y') ?? '-',
            'distribusi_kualitas' => [
                'baik' => $pencatatan->where('kualitas', 'baik')->count(),
                'sedang' => $pencatatan->where('kualitas', 'sedang')->count(),
                'buruk' => $pencatatan->where('kualitas', 'buruk')->count()
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
