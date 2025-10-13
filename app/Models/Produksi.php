<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $table = 'produksi';

    protected $fillable = [
        'kandang_id', 'batch_produksi_id', 'tanggal', 'jumlah_telur', 'berat_rata_rata', 'harga_per_pcs', 'catatan'
    ];

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    // Backwards-compatible accessor: expose 'tanggal' for views that expect it
    public function getTanggalAttribute()
    {
        // prefer 'tanggal_mulai' (exists in schema), fallback to null
        return $this->attributes['tanggal_mulai'] ?? null;
    }
}
