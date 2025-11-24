<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penetasan extends Model
{
    protected $table = 'penetasan';

    // the migrations renamed created_at/updated_at -> dibuat_pada/diperbarui_pada
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'batch',
        'kandang_id',
        'tanggal_simpan_telur',
        'estimasi_tanggal_menetas',
        'jumlah_telur',
        'tanggal_menetas',
        'jumlah_menetas',
        'jumlah_doc',
        'suhu_penetasan',
        'kelembaban_penetasan',
        'telur_tidak_fertil',
        'persentase_tetas',
        'catatan',
        'status',
        'doc_ditransfer',
        'telur_infertil_ditransfer',
    ];

    protected $casts = [
        'tanggal_simpan_telur' => 'date',
        'estimasi_tanggal_menetas' => 'date',
        'tanggal_menetas' => 'date',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    /**
     * Relasi ke tabel kandang
     */
    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    /**
     * Relasi ke pembesaran (one-to-many)
     */
    public function pembesaran()
    {
        return $this->hasMany(Pembesaran::class, 'penetasan_id');
    }

    /**
     * Relasi ke produksi (untuk telur infertil)
     */
    public function produksi()
    {
        return $this->hasMany(Produksi::class, 'penetasan_id');
    }

    /**
     * Hitung DOC yang tersedia untuk ditransfer ke pembesaran
     */
    public function getDocTersediaAttribute()
    {
        return ($this->jumlah_doc ?? 0) - ($this->doc_ditransfer ?? 0);
    }

    /**
     * Hitung telur infertil yang tersedia untuk produksi
     */
    public function getTelurInfertilTersediaAttribute()
    {
        return ($this->telur_tidak_fertil ?? 0) - ($this->telur_infertil_ditransfer ?? 0);
    }
}
