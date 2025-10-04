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
    ];

    protected $casts = [
        'tanggal_simpan_telur' => 'date',
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
}
