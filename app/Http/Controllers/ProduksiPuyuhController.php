<?php

namespace App\Http\Controllers;

use App\Models\Produksi;

class ProduksiPuyuhController extends ProduksiController
{
    /**
     * Tampilkan detail produksi khusus tipe puyuh.
     */
    public function show(Produksi $produksi)
    {
        if ($produksi->tipe_produksi !== 'puyuh') {
            abort(404, 'Batch produksi bukan tipe puyuh.');
        }

        return parent::show($produksi);
    }
}
