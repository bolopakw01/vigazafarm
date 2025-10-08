<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk laporan harian per batch
 */
class LaporanHarian extends Model
{
    protected $table = 'laporan_harian';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'batch_produksi_id',
        'tanggal',
        'jumlah_burung',
        'produksi_telur',
        'jumlah_kematian',
        'konsumsi_pakan_kg',
        'fcr',
        'hen_day_production',
        'mortalitas_kumulatif',
        'catatan_kejadian',
        'pengguna_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_burung' => 'integer',
        'produksi_telur' => 'integer',
        'jumlah_kematian' => 'integer',
        'konsumsi_pakan_kg' => 'decimal:2',
        'fcr' => 'decimal:2',
        'hen_day_production' => 'decimal:2',
        'mortalitas_kumulatif' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    /**
     * Relasi ke batch produksi
     */
    public function batchProduksi()
    {
        return $this->belongsTo(BatchProduksi::class, 'batch_produksi_id');
    }

    /**
     * Relasi ke pengguna (yang membuat laporan)
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
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
     * Get laporan untuk tanggal tertentu
     */
    public static function getLaporanHarian($batchId, $date)
    {
        return self::where('batch_produksi_id', $batchId)
            ->whereDate('tanggal', $date)
            ->first();
    }

    /**
     * Generate laporan harian otomatis
     * Berdasarkan data pakan dan kematian yang sudah diinput
     */
    public static function generateLaporanHarian($batchId, $date, $userId = null)
    {
        // Ensure userId is integer or null
        if ($userId !== null) {
            $userId = (int) $userId;
        }
        
        // Get pembesaran data
        $pembesaran = Pembesaran::where('batch_produksi_id', $batchId)->first();
        
        if (!$pembesaran) {
            return null;
        }

        // Hitung populasi awal
        $populasiAwal = $pembesaran->jumlah_anak_ayam;
        
        // Hitung total kematian sampai tanggal ini
        $totalKematian = Kematian::where('batch_produksi_id', $batchId)
            ->where('tanggal', '<=', $date)
            ->sum('jumlah');
        
        // Hitung kematian hari ini
        $kematianHariIni = Kematian::getKematianHarian($batchId, $date);
        
        // Hitung konsumsi pakan hari ini
        $konsumsiPakanHariIni = Pakan::getKonsumsiHarian($batchId, $date);
        
        // Hitung populasi saat ini
        $populasiSaatIni = $populasiAwal - $totalKematian;
        
        // Hitung mortalitas kumulatif
        $mortalitasKumulatif = Kematian::hitungMortalitasKumulatif($batchId, $populasiAwal);
        
        // Hitung FCR (butuh data produksi telur)
        $fcr = null;
        // TODO: Calculate FCR jika sudah fase produksi
        
        // Hitung HDP (Hen Day Production)
        $hdp = null;
        // TODO: Calculate HDP jika sudah fase produksi
        
        // Create or update laporan
        return self::updateOrCreate(
            [
                'batch_produksi_id' => $batchId,
                'tanggal' => $date,
            ],
            [
                'jumlah_burung' => $populasiSaatIni,
                'produksi_telur' => 0, // Update jika ada data produksi
                'jumlah_kematian' => $kematianHariIni,
                'konsumsi_pakan_kg' => $konsumsiPakanHariIni,
                'fcr' => $fcr,
                'hen_day_production' => $hdp,
                'mortalitas_kumulatif' => $mortalitasKumulatif,
                'pengguna_id' => $userId,
            ]
        );
    }

    /**
     * Get summary mingguan
     */
    public static function getSummaryMingguan($batchId, $startDate, $endDate)
    {
        return self::where('batch_produksi_id', $batchId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('
                AVG(jumlah_burung) as rata_populasi,
                SUM(produksi_telur) as total_telur,
                SUM(jumlah_kematian) as total_kematian,
                SUM(konsumsi_pakan_kg) as total_pakan,
                AVG(fcr) as rata_fcr,
                AVG(hen_day_production) as rata_hdp,
                MAX(mortalitas_kumulatif) as mortalitas_akhir
            ')
            ->first();
    }
}
