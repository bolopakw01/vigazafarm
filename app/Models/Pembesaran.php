<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembesaran extends Model
{
    protected $table = 'vf_pembesaran';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kandang_id',
        'batch_produksi_id',
        'penetasan_id',
        'tanggal_masuk',
        'jumlah_anak_ayam',
        'jenis_kelamin',
        'status_batch',
        'tanggal_selesai',
        'tanggal_siap',
        'jumlah_siap',
        'umur_hari',
        'berat_rata_rata',
        'target_berat_akhir',
        'kondisi_doc',
        'catatan',
        'indukan_ditransfer',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_siap' => 'date',
        'tanggal_selesai' => 'date',
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

    /**
     * Relasi ke produksi (untuk indukan yang siap produksi)
     */
    public function produksi()
    {
        return $this->hasMany(Produksi::class, 'pembesaran_id');
    }

    /**
     * Hitung indukan yang tersedia untuk ditransfer ke produksi
     */
    public function getIndukanTersediaAttribute()
    {
        return ($this->jumlah_siap ?? 0) - ($this->indukan_ditransfer ?? 0);
    }
}
