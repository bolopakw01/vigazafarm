<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penetasan;
use App\Models\Kandang;
use Illuminate\Support\Facades\Auth;

class PenetasanController extends Controller
{
    public function create()
    {
        $kandang = Kandang::orderBy('nama_kandang')->get();
        return view('admin.pages.penetasan.create-penetasan', compact('kandang'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'tanggal_simpan_telur' => 'required|date',
            'jumlah_telur' => 'required|integer|min:1',
            'tanggal_menetas' => 'nullable|date|after_or_equal:tanggal_simpan_telur',
            'jumlah_menetas' => 'nullable|integer|min:0',
            'jumlah_doc' => 'nullable|integer|min:0',
            'suhu_penetasan' => 'nullable|numeric|min:0|max:50',
            'kelembaban_penetasan' => 'nullable|numeric|min:0|max:100',
            'telur_tidak_fertil' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
        ]);

        // Auto-generate unique batch code
        $data['batch'] = $this->generateUniqueBatch();

        // Calculate persentase_tetas if data is complete
        if (isset($data['jumlah_telur']) && isset($data['jumlah_menetas']) && $data['jumlah_telur'] > 0) {
            $data['persentase_tetas'] = ($data['jumlah_menetas'] / $data['jumlah_telur']) * 100;
        }

        // Set status default to 'proses'
        $data['status'] = 'proses';

        Penetasan::create($data);

        return redirect()->route('admin.penetasan')->with('success', 'Data penetasan berhasil ditambahkan.');
    }

    /**
     * Generate unique batch code for penetasan
     */
    private function generateUniqueBatch()
    {
        do {
            // Format: PTN-YYYYMMDD-XXX (PTN = Penetasan)
            $date = date('Ymd');
            $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            $batch = "PTN-{$date}-{$random}";
            
            // Check if batch already exists
            $exists = Penetasan::where('batch', $batch)->exists();
        } while ($exists);

        return $batch;
    }

    public function show(Penetasan $penetasan)
    {
        return view('admin.pages.penetasan.show', compact('penetasan'));
    }

    public function edit(Penetasan $penetasan)
    {
        $kandang = Kandang::orderBy('nama_kandang')->get();
        return view('admin.pages.penetasan.edit-penetasan', compact('penetasan', 'kandang'));
    }

    public function update(Request $request, Penetasan $penetasan)
    {
        $data = $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'tanggal_simpan_telur' => 'required|date',
            'jumlah_telur' => 'required|integer|min:1',
            'tanggal_menetas' => 'nullable|date|after_or_equal:tanggal_simpan_telur',
            'jumlah_menetas' => 'nullable|integer|min:0',
            'jumlah_doc' => 'nullable|integer|min:0',
            'suhu_penetasan' => 'nullable|numeric|min:0|max:50',
            'kelembaban_penetasan' => 'nullable|numeric|min:0|max:100',
            'telur_tidak_fertil' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
            'status' => 'nullable|in:proses,selesai,gagal',
        ]);

        // Calculate persentase_tetas if data is complete
        if (isset($data['jumlah_telur']) && isset($data['jumlah_menetas']) && $data['jumlah_telur'] > 0) {
            $data['persentase_tetas'] = ($data['jumlah_menetas'] / $data['jumlah_telur']) * 100;
        }

        // Auto-set status to selesai if tanggal_menetas is filled and status not manually set
        if (!empty($data['tanggal_menetas']) && !isset($data['status'])) {
            $data['status'] = 'selesai';
        }

        $penetasan->update($data);

        return redirect()->route('admin.penetasan')->with('success', 'Data berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Penetasan $penetasan)
    {
        // Only owner can update status
    if (Auth::user()->peran !== 'owner') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:proses,selesai,gagal',
        ]);

        $penetasan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'status' => $penetasan->status
        ]);
    }


    public function destroy(Penetasan $penetasan)
    {
        $penetasan->delete();
        return redirect()->route('admin.penetasan')->with('success', 'Data berhasil dihapus.');
    }
}
