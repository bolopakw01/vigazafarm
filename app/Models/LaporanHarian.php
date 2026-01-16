<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk laporan harian per batch
 */
class LaporanHarian extends Model
{
    protected $table = 'vf_laporan_harian';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'batch_produksi_id',
        'tanggal',
        'jumlah_burung',
        'produksi_telur',
        'nama_tray',
        'keterangan_tray',
        'jumlah_kematian',
        'jumlah_kematian_jantan',
        'jumlah_kematian_betina',
        'jenis_kelamin_kematian',
        'keterangan_kematian',
        'konsumsi_pakan_kg',
        'sisa_pakan_kg',
        'harga_pakan_per_kg',
        'biaya_pakan_harian',
        'sisa_tray_bal',
        'sisa_tray_lembar',
        'sisa_vitamin_liter',
        'vitamin_terpakai',
        'harga_vitamin_per_liter',
        'biaya_vitamin_harian',
        'sisa_telur',
        'penjualan_telur_butir',
        'penjualan_puyuh_ekor',
        'jenis_kelamin_penjualan',
        'pendapatan_harian',
        'tray_penjualan_id',
        'harga_per_butir',
        'nama_tray_penjualan',
        'fcr',
        'hen_day_production',
        'mortalitas_kumulatif',
        'catatan_kejadian',
        'tampilkan_di_histori',
        'pengguna_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_burung' => 'integer',
        'produksi_telur' => 'integer',
        'nama_tray' => 'string',
        'keterangan_tray' => 'string',
        'jumlah_kematian' => 'integer',
        'jumlah_kematian_jantan' => 'integer',
        'jumlah_kematian_betina' => 'integer',
        'jenis_kelamin_kematian' => 'string',
        'keterangan_kematian' => 'string',
        'konsumsi_pakan_kg' => 'decimal:2',
        'sisa_pakan_kg' => 'decimal:2',
        'harga_pakan_per_kg' => 'decimal:2',
        'biaya_pakan_harian' => 'decimal:2',
        'sisa_tray_bal' => 'decimal:2',
        'sisa_tray_lembar' => 'integer',
        'sisa_vitamin_liter' => 'decimal:2',
        'vitamin_terpakai' => 'decimal:3',
        'harga_vitamin_per_liter' => 'decimal:2',
        'biaya_vitamin_harian' => 'decimal:2',
        'sisa_telur' => 'integer',
        'penjualan_telur_butir' => 'integer',
        'penjualan_puyuh_ekor' => 'integer',
        'jenis_kelamin_penjualan' => 'string',
        'pendapatan_harian' => 'decimal:2',
        'tray_penjualan_id' => 'integer',
        'harga_per_butir' => 'decimal:2',
        'nama_tray_penjualan' => 'string',
        'fcr' => 'decimal:2',
        'hen_day_production' => 'decimal:2',
        'mortalitas_kumulatif' => 'decimal:2',
        'tampilkan_di_histori' => 'boolean',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    /**
     * Relasi ke batch produksi
     */
    public function batchProduksi()
    {
    return $this->belongsTo(Produksi::class, 'batch_produksi_id', 'batch_produksi_id');
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
