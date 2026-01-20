<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk pencatatan kematian burung
 */
class Kematian extends Model
{
    protected $table = 'vf_kematian';

    const PENYEBAB_PENYAKIT = 'penyakit';
    const PENYEBAB_STRESS = 'stress';
    const PENYEBAB_KECELAKAAN = 'kecelakaan';
    const PENYEBAB_USIA = 'usia';
    const PENYEBAB_TIDAK_DIKETAHUI = 'tidak_diketahui';

    protected $fillable = [
        'produksi_id',
        'batch_produksi_id',
        'tanggal',
        'jumlah',
        'penyebab',
        'keterangan',
        'pengguna_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke produksi
     */
    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    /**
     * Relasi ke batch produksi
     */
    public function batchProduksi()
    {
        return $this->belongsTo(BatchProduksi::class, 'batch_produksi_id');
    }

    /**
     * Relasi ke pengguna pencatat.
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    /**
     * Get all available penyebab options
     */
    public static function getPenyebabOptions()
    {
        return [
            self::PENYEBAB_PENYAKIT => 'Penyakit',
            self::PENYEBAB_STRESS => 'Stress',
            self::PENYEBAB_KECELAKAAN => 'Kecelakaan',
            self::PENYEBAB_USIA => 'Usia Tua',
            self::PENYEBAB_TIDAK_DIKETAHUI => 'Tidak Diketahui',
        ];
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
     * Hitung total kematian untuk batch tertentu
     */
    public static function totalKematianByBatch($batchId)
    {
        return self::where('batch_produksi_id', $batchId)
            ->sum('jumlah');
    }

    /**
     * Hitung kematian harian
     */
    public static function getKematianHarian($batchId, $date)
    {
        return self::where('batch_produksi_id', $batchId)
            ->whereDate('tanggal', $date)
            ->sum('jumlah');
    }

    /**
     * Hitung mortalitas kumulatif
     * 
     * @param int $batchId
     * @param int $populasiAwal
     * @return float Persentase mortalitas
     */
    public static function hitungMortalitasKumulatif($batchId, $populasiAwal)
    {
        $totalMati = self::totalKematianByBatch($batchId);
        
        if ($populasiAwal == 0) {
            return 0;
        }
        
        return round(($totalMati / $populasiAwal) * 100, 2);
    }

    /**
     * Get statistik penyebab kematian
     */
    public static function getStatistikPenyebab($batchId)
    {
        return self::where('batch_produksi_id', $batchId)
            ->selectRaw('penyebab, SUM(jumlah) as total')
            ->groupBy('penyebab')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Check if mortalitas harian tinggi (> 5%)
     */
    public static function isMortalitasTinggi($batchId, $date, $populasiSaatIni)
    {
        $kematianHarian = self::getKematianHarian($batchId, $date);
        
        if ($populasiSaatIni == 0) {
            return false;
        }
        
        $mortalitasHarian = ($kematianHarian / $populasiSaatIni) * 100;
        
        return $mortalitasHarian > 5;
    }
}
