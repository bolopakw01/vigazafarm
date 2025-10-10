<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeratSampling extends Model
{
    protected $table = 'berat_sampling';
    
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'batch_produksi_id',
        'tanggal_sampling',
        'umur_hari',
        'berat_rata_rata',
        'jumlah_sampel',
        'catatan',
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
}
