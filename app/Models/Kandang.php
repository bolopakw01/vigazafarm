<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kandang extends Model
{
    use SoftDeletes;

    protected $table = 'vf_kandang';

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
     * Nama kandang lengkap beserta tipe dan kapasitas.
     */
    public function getNamaDenganDetailAttribute(): string
    {
        $typeRaw = $this->tipe_kandang ?? $this->tipe ?? '';
        $typeRaw = is_string($typeRaw) ? trim($typeRaw) : '';
        $typeLabel = $typeRaw !== '' ? ucwords(strtolower($typeRaw)) : 'Tidak diketahui';

        $capacityRaw = $this->kapasitas_maksimal ?? $this->kapasitas ?? null;
        $capacityLabel = '-';
        if ($capacityRaw !== null && $capacityRaw !== '') {
            $capacityNumeric = is_numeric($capacityRaw) ? (float) $capacityRaw : null;
            if ($capacityNumeric !== null) {
                $capacityLabel = number_format($capacityNumeric) . ' ekor';
            }
        }

        $name = $this->nama_kandang ?? '-';

        return sprintf('%s (%s, %s)', $name, $typeLabel, $capacityLabel);
    }

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
        return $this->hasMany('App\Models\BatchProduksi', 'kandang_id');
    }

    /**
     * Relasi ke tabel produksi
     */
    public function produksi()
    {
        return $this->hasMany(Produksi::class, 'kandang_id');
    }

    /**
     * Relasi ke tabel monitoring_lingkungan
     */
    public function monitoringLingkungan()
    {
        return $this->hasMany(MonitoringLingkungan::class, 'kandang_id');
    }

    /**
     * Relasi ke tabel pembesaran
     */
    public function pembesaran()
    {
        return $this->hasMany(Pembesaran::class, 'kandang_id');
    }

    /**
     * Hitung kapasitas terpakai berdasarkan tipe kandang
     */
    public function getKapasitasTerpakaiAttribute()
    {
        switch (strtolower($this->tipe_kandang)) {
            case 'penetasan':
                // Jumlah telur yang disimpan atau menetas
                return $this->penetasan()->where('status', 'aktif')->sum('jumlah_telur');
            case 'pembesaran':
                // Jumlah anak ayam di pembesaran aktif
                return $this->pembesaran()->where('status_batch', 'aktif')->sum('jumlah_anak_ayam');
            case 'produksi':
                // Jumlah indukan di produksi aktif
                return $this->produksi()->where('status', 'aktif')->sum('jumlah_indukan');
            default:
                return 0;
        }
    }
}
