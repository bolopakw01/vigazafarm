<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembesaran extends Model
{
    protected $table = 'pembesaran';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kandang_id',
        'batch_produksi_id',
        'penetasan_id',
        'tanggal_masuk',
        'jumlah_anak_ayam',
        'jenis_kelamin',
        'tanggal_siap',
        'jumlah_siap',
        'umur_hari',
        'berat_rata_rata',
        'target_berat_akhir',
        'kondisi_doc',
        'catatan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_siap' => 'date',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    /**
     * Relasi ke kandang
     */
    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    /**
     * Relasi ke penetasan (sumber DOC)
     */
    public function penetasan()
    {
        return $this->belongsTo(Penetasan::class, 'penetasan_id');
    }
}
