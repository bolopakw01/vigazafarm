<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrayHistory extends Model
{
    protected $table = 'vf_tray_histories';

    protected $fillable = [
        'produksi_id',
        'laporan_harian_id',
        'action',
        'nama_tray',
        'tanggal',
        'jumlah_telur',
        'keterangan',
        'old_nama_tray',
        'old_jumlah_telur',
        'old_keterangan',
        'pengguna_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_telur' => 'integer',
        'old_jumlah_telur' => 'integer',
    ];

    public function produksi(): BelongsTo
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    public function laporanHarian(): BelongsTo
    {
        return $this->belongsTo(LaporanHarian::class, 'laporan_harian_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}
