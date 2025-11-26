<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk monitoring lingkungan kandang
 */
class MonitoringLingkungan extends Model
{
    protected $table = 'vf_monitoring_lingkungan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    const VENTILASI_BAIK = 'Baik';
    const VENTILASI_CUKUP = 'Cukup';
    const VENTILASI_KURANG = 'Kurang';

    protected $fillable = [
        'kandang_id',
        'batch_produksi_id',
        'waktu_pencatatan',
        'suhu',
        'kelembaban',
        'intensitas_cahaya',
        'kondisi_ventilasi',
        'catatan',
        'pengguna_id',
    ];

    protected $casts = [
        'waktu_pencatatan' => 'datetime',
        'suhu' => 'decimal:2',
        'kelembaban' => 'decimal:2',
        'intensitas_cahaya' => 'decimal:2',
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
     * Get ventilasi options
     */
    public static function getVentilasiOptions()
    {
        return [
            self::VENTILASI_BAIK => 'Baik',
            self::VENTILASI_CUKUP => 'Cukup',
            self::VENTILASI_KURANG => 'Kurang',
        ];
    }

    /**
     * Scope untuk filter berdasarkan kandang
     */
    public function scopeByKandang($query, $kandangId)
    {
        return $query->where('kandang_id', $kandangId);
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
        return $query->whereBetween('waktu_pencatatan', [$startDate, $endDate]);
    }

    /**
     * Get rata-rata kondisi lingkungan untuk periode tertentu
     */
    public static function getRataRataLingkungan($kandangId, $startDate, $endDate)
    {
        return self::where('kandang_id', $kandangId)
            ->whereBetween('waktu_pencatatan', [$startDate, $endDate])
            ->selectRaw('
                AVG(suhu) as rata_suhu,
                MIN(suhu) as suhu_min,
                MAX(suhu) as suhu_max,
                AVG(kelembaban) as rata_kelembaban,
                MIN(kelembaban) as kelembaban_min,
                MAX(kelembaban) as kelembaban_max,
                AVG(intensitas_cahaya) as rata_cahaya,
                COUNT(*) as jumlah_pencatatan
            ')
            ->first();
    }

    /**
     * Check apakah suhu dalam range optimal (grower: 24-28°C)
     */
    public function isSuhuOptimal($fase = 'grower')
    {
        $standards = [
            'DOC' => ['min' => 32, 'max' => 38],
            'grower' => ['min' => 24, 'max' => 28],
            'layer' => ['min' => 20, 'max' => 27],
        ];

        $std = $standards[$fase] ?? $standards['grower'];

        return $this->suhu >= $std['min'] && $this->suhu <= $std['max'];
    }

    /**
     * Check apakah kelembaban dalam range optimal (grower: 55-65%)
     */
    public function isKelembabanOptimal($fase = 'grower')
    {
        $standards = [
            'DOC' => ['min' => 60, 'max' => 70],
            'grower' => ['min' => 55, 'max' => 65],
            'layer' => ['min' => 50, 'max' => 70],
        ];

        $std = $standards[$fase] ?? $standards['grower'];

        return $this->kelembaban >= $std['min'] && $this->kelembaban <= $std['max'];
    }

    /**
     * Get status lingkungan (normal, warning, danger)
     */
    public function getStatusLingkungan($fase = 'grower')
    {
        $isSuhuOk = $this->isSuhuOptimal($fase);
        $isKelembabanOk = $this->isKelembabanOptimal($fase);

        if ($isSuhuOk && $isKelembabanOk) {
            return [
                'status' => 'normal',
                'badge' => 'success',
                'message' => 'Kondisi lingkungan optimal'
            ];
        }

        if (!$isSuhuOk || !$isKelembabanOk) {
            return [
                'status' => 'warning',
                'badge' => 'warning',
                'message' => 'Kondisi lingkungan perlu perhatian'
            ];
        }

        return [
            'status' => 'danger',
            'badge' => 'danger',
            'message' => 'Kondisi lingkungan tidak optimal'
        ];
    }

    /**
     * Get summary mingguan monitoring lingkungan
     */
    public static function getSummaryMingguan($kandangId, $batchId = null)
    {
        $query = self::where('kandang_id', $kandangId)
            ->where('waktu_pencatatan', '>=', now()->subDays(7));

        if ($batchId) {
            $query->where('batch_produksi_id', $batchId);
        }

        return $query->selectRaw('
                DATE(waktu_pencatatan) as tanggal,
                AVG(suhu) as rata_suhu,
                AVG(kelembaban) as rata_kelembaban,
                AVG(intensitas_cahaya) as rata_cahaya,
                COUNT(*) as jumlah_pencatatan
            ')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();
    }
}
