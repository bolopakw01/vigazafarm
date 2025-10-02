<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kandang extends Model
{
    use SoftDeletes;

    protected $table = 'kandang';

    protected $fillable = [
        'kode_kandang',
        'nama_kandang',
        'kapasitas_maksimal',
        'tipe_kandang',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel penetasan
     */
    public function penetasan()
    {
        return $this->hasMany(Penetasan::class, 'kandang_id');
    }

    /**
     * Relasi ke tabel batch_produksi
     */
    public function batchProduksi()
    {
        return $this->hasMany(BatchProduksi::class, 'kandang_id');
    }

    /**
     * Relasi ke tabel monitoring_lingkungan
     */
    public function monitoringLingkungan()
    {
        return $this->hasMany(MonitoringLingkungan::class, 'kandang_id');
    }
}
