<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kandang;

/**
 * ==========================================
 * Controller : KandangController
 * Deskripsi  : Mengelola CRUD data kandang beserta pencarian dan paginasi untuk tampilan admin.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class KandangController extends Controller
{
    public function index(Request $request)
    {
        /**
         * Menampilkan daftar kandang dengan dukungan pencarian dan paginasi.
         */
        $perPage = $request->get('per_page', 5);
        $search = $request->get('search', '');

        $query = Kandang::with(['penetasan', 'pembesaran', 'produksi']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_kandang', 'like', "%{$search}%")
                  ->orWhere('nama_kandang', 'like', "%{$search}%")
                  ->orWhere('tipe_kandang', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $kandang = $perPage === 'all'
            ? $query->orderBy('nama_kandang')->get()
            : $query->orderBy('nama_kandang')->paginate($perPage);

        return view('admin.pages.kandang.index-kandang', compact('kandang'));
    }

    public function create()
    {
        /**
         * Menampilkan form pembuatan data kandang baru.
         */
        return view('admin.pages.kandang.create-kandang');
    }

    public function store(Request $request)
    {
        /**
         * Memvalidasi dan menyimpan data kandang baru ke database.
         */
        $data = $request->validate([
            'nama_kandang' => 'required|string|max:191',
            'kapasitas_maksimal' => 'nullable|integer',
            'tipe_kandang' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:100',
        ]);

        // Generate kode_kandang secara otomatis jika tidak disediakan
        if (!isset($data['kode_kandang']) || empty($data['kode_kandang'])) {
            $data['kode_kandang'] = 'KDG-' . str_pad((Kandang::count() + 1), 3, '0', STR_PAD_LEFT);
        }

        Kandang::create($data);

        return redirect()->route('admin.kandang')->with('success', 'Kandang berhasil dibuat');
    }

    public function show(Kandang $kandang)
    {
        /**
         * Menampilkan detail sebuah kandang.
         */
        return view('admin.pages.kandang.show-kandang', compact('kandang'));
    }

    public function edit(Kandang $kandang)
    {
        /**
         * Menampilkan form edit untuk data kandang yang dipilih.
         */
        return view('admin.pages.kandang.edit-kandang', compact('kandang'));
    }

    public function update(Request $request, Kandang $kandang)
    {
        /**
         * Memvalidasi dan memperbarui data kandang yang dipilih.
         */
        $data = $request->validate([
            'nama_kandang' => 'required|string|max:191',
            'kapasitas_maksimal' => 'nullable|integer',
            'tipe_kandang' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:100',
        ]);

        // Generate kode_kandang secara otomatis jika tidak disediakan dan record saat ini tidak memilikinya
        if (!isset($data['kode_kandang']) || empty($data['kode_kandang'])) {
            if (empty($kandang->kode_kandang)) {
                $data['kode_kandang'] = 'KDG-' . str_pad((Kandang::count() + 1), 3, '0', STR_PAD_LEFT);
            }
        }

        $kandang->update($data);

        return redirect()->route('admin.kandang')->with('success', 'Kandang berhasil diperbarui');
    }

    public function destroy(Kandang $kandang)
    {
        /**
         * Menghapus data kandang dari database.
         */
        $kandang->delete();
        return redirect()->route('admin.kandang')->with('success', 'Kandang berhasil dihapus');
    }
}
