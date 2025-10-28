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
        'tipe_produksi', // telur, puyuh
        'jenis_input',
        'tanggal_mulai',
        'tanggal_akhir',
        'tanggal', 
        'jumlah_telur', 
        'jumlah_indukan',
        'umur_mulai_produksi',
        'berat_rata_rata', 
        'berat_rata_telur',
        'harga_per_pcs', 
        'harga_per_kg',
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

    /**
     * Relasi ke pencatatan produksi harian
     */
    public function pencatatanProduksi()
    {
        return $this->hasMany(PencatatanProduksi::class, 'produksi_id');
    }
}
