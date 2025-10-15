<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kandang;

class KandangController extends Controller
{
    public function index(Request $request)
    {
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
        return view('admin.pages.kandang.create-kandang');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kandang' => 'required|string|max:191',
            'kapasitas_maksimal' => 'nullable|integer',
            'tipe_kandang' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
        ]);

        // Generate kode_kandang automatically if not provided
        if (!isset($data['kode_kandang']) || empty($data['kode_kandang'])) {
            $data['kode_kandang'] = 'KDG-' . str_pad((Kandang::count() + 1), 3, '0', STR_PAD_LEFT);
        }

        Kandang::create($data);

        return redirect()->route('admin.kandang')->with('success', 'Kandang berhasil dibuat');
    }

    public function show(Kandang $kandang)
    {
        return view('admin.pages.kandang.show-kandang', compact('kandang'));
    }

    public function edit(Kandang $kandang)
    {
        return view('admin.pages.kandang.edit-kandang', compact('kandang'));
    }

    public function update(Request $request, Kandang $kandang)
    {
        $data = $request->validate([
            'nama_kandang' => 'required|string|max:191',
            'kapasitas_maksimal' => 'nullable|integer',
            'tipe_kandang' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
        ]);

        // Generate kode_kandang automatically if not provided and current record doesn't have one
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
        $kandang->delete();
        return redirect()->route('admin.kandang')->with('success', 'Kandang berhasil dihapus');
    }
}
