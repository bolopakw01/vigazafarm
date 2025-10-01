<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penetasan;

class PenetasanController extends Controller
{
    public function show(Penetasan $penetasan)
    {
        return view('admin.pages.penetasan.show', compact('penetasan'));
    }

    public function edit(Penetasan $penetasan)
    {
        return view('admin.pages.penetasan.edit', compact('penetasan'));
    }

    public function update(Request $request, Penetasan $penetasan)
    {
        $data = $request->validate([
            'tanggal_simpan_telur' => 'nullable|date',
            'jumlah_telur' => 'nullable|integer',
            'tanggal_menetas' => 'nullable|date',
            'jumlah_menetas' => 'nullable|integer',
            'jumlah_doc' => 'nullable|integer',
        ]);

        $penetasan->update($data);

        return redirect()->route('admin.penetasan')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(Penetasan $penetasan)
    {
        $penetasan->delete();
        return redirect()->route('admin.penetasan')->with('success', 'Data berhasil dihapus.');
    }
}
