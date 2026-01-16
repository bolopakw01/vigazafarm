<?php

namespace App\Http\Controllers;

use App\Models\Produksi;

class ProduksiTelurController extends ProduksiController
{
    /**
     * Tampilkan detail produksi khusus tipe telur.
     */
    public function show(Produksi $produksi)
    {
        if ($produksi->tipe_produksi !== 'telur') {
            abort(404, 'Batch produksi bukan tipe telur.');
        }

        return parent::show($produksi);
    }
}
