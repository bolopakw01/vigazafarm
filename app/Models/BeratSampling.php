<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeratSampling extends Model
{
    protected $table = 'vf_berat_sampling';

    protected $fillable = [
        'batch_produksi_id',
        'tanggal_sampling',
        'umur_hari',
        'berat_rata_rata',
        'jumlah_sampel',
        'catatan',
        'pengguna_id',
    ];

    protected $casts = [
        'tanggal_sampling' => 'date',
        'berat_rata_rata' => 'decimal:2',
    ];

    /**
     * Relationship: Batch Produksi
     */
    public function batchProduksi()
    {
        return $this->belongsTo(BatchProduksi::class, 'batch_produksi_id', 'id');
    }

    /**
     * Relasi ke pengguna pencatat.
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}
