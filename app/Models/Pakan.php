<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk pencatatan konsumsi pakan harian
 */
class Pakan extends Model
{
    protected $table = 'vf_pakan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'produksi_id',
        'stok_pakan_id',
        'feed_item_id',
        'batch_produksi_id',
        'tanggal',
        'jumlah_kg',
        'sisa_pakan_kg',
        'jumlah_karung',
        'harga_per_kg',
        'total_biaya',
        'pengguna_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_kg' => 'decimal:2',
        'sisa_pakan_kg' => 'decimal:2',
        'jumlah_karung' => 'integer',
        'harga_per_kg' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    /**
     * Relasi ke produksi
     */
    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    /**
     * Relasi ke stok pakan
     */
    public function stokPakan()
    {
        return $this->belongsTo(StokPakan::class, 'stok_pakan_id');
    }

    /**
     * Relasi ke master Set Pakan & Vitamin
     */
    public function feedItem()
    {
        return $this->belongsTo(FeedVitaminItem::class, 'feed_item_id');
    }

    /**
     * Relasi ke batch produksi
     */
    public function batchProduksi()
    {
        return $this->belongsTo(BatchProduksi::class, 'batch_produksi_id');
    }

    /**
     * Relasi ke pengguna yang mencatat.
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    /**
     * Scope untuk filter berdasarkan batch
     */
    public function scopeByBatch($query, $batchId)
    {
        return $query->where('batch_produksi_id', $batchId);
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Hitung total konsumsi pakan untuk batch tertentu
     */
    public static function totalKonsumsiByBatch($batchId)
    {
        return self::where('batch_produksi_id', $batchId)
            ->sum('jumlah_kg');
    }

    /**
     * Hitung total biaya pakan untuk batch tertentu
     */
    public static function totalBiayaByBatch($batchId)
    {
        return self::where('batch_produksi_id', $batchId)
            ->sum('total_biaya');
    }

    /**
     * Get konsumsi pakan harian
     */
    public static function getKonsumsiHarian($batchId, $date)
    {
        return self::where('batch_produksi_id', $batchId)
            ->whereDate('tanggal', $date)
            ->sum('jumlah_kg');
    }

    /**
     * Mutator: Auto-calculate total_biaya saat save
     */
    protected static function booted()
    {
        static::saving(function ($pakan) {
            if ($pakan->jumlah_kg && $pakan->harga_per_kg) {
                $pakan->total_biaya = $pakan->jumlah_kg * $pakan->harga_per_kg;
            }
        });
    }
}
