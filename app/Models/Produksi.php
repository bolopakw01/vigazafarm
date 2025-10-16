<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $table = 'produksi';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kandang_id', 
        'batch_produksi_id', 
        'penetasan_id',
        'pembesaran_id',
        'tanggal_mulai',
        'tanggal_akhir',
        'tanggal', 
        'jumlah_telur', 
        'jumlah_indukan',
        'umur_mulai_produksi',
        'berat_rata_rata', 
        'harga_per_pcs', 
        'status',
        'catatan'
    ];

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    /**
     * Relasi ke penetasan (untuk telur infertil yang langsung ke produksi)
     */
    public function penetasan()
    {
        return $this->belongsTo(Penetasan::class, 'penetasan_id');
    }

    /**
     * Relasi ke pembesaran (untuk indukan yang dari pembesaran)
     */
    public function pembesaran()
    {
        return $this->belongsTo(Pembesaran::class, 'pembesaran_id');
    }

    // Backwards-compatible accessor: expose 'tanggal' for views that expect it
    public function getTanggalAttribute()
    {
        // prefer 'tanggal_mulai' (exists in schema), fallback to null
        return $this->attributes['tanggal_mulai'] ?? $this->attributes['tanggal'] ?? null;
    }
}
