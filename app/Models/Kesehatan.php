<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk pencatatan kegiatan kesehatan (vaksinasi, pengobatan, dll)
 */
class Kesehatan extends Model
{
    protected $table = 'vf_kesehatan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    const TIPE_VAKSINASI = 'vaksinasi';
    const TIPE_PENGOBATAN = 'pengobatan';
    const TIPE_PEMERIKSAAN = 'pemeriksaan_rutin';
    const TIPE_KARANTINA = 'karantina';
    const TIPE_VITAMIN = 'vitamin';

    protected $fillable = [
        'batch_produksi_id',
        'tanggal',
        'tipe_kegiatan',
        'nama_vaksin_obat',
        'jumlah_burung',
        'kandang_tujuan_id',
        'karantina_dikembalikan',
        'karantina_dikembalikan_pada',
        'feed_vitamin_item_id',
        'catatan',
        'biaya',
        'petugas',
        'pengguna_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_burung' => 'integer',
        'karantina_dikembalikan' => 'boolean',
        'karantina_dikembalikan_pada' => 'datetime',
        'biaya' => 'decimal:2',
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
     * Relasi ke pengguna pencatat.
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function kandangTujuan()
    {
        return $this->belongsTo(Kandang::class, 'kandang_tujuan_id');
    }

    public function vitaminItem()
    {
        return $this->belongsTo(FeedVitaminItem::class, 'feed_vitamin_item_id');
    }

    /**
     * Get tipe kegiatan options
     */
    public static function getTipeKegiatanOptions()
    {
        return [
            self::TIPE_VAKSINASI => 'Vaksinasi',
            self::TIPE_PENGOBATAN => 'Pengobatan',
            self::TIPE_PEMERIKSAAN => 'Pemeriksaan Rutin',
            self::TIPE_VITAMIN => 'Vitamin',
            self::TIPE_KARANTINA => 'Karantina',
        ];
    }

    /**
     * Scope untuk filter berdasarkan batch
     */
    public function scopeByBatch($query, $batchId)
    {
        return $query->where('batch_produksi_id', $batchId);
    }

    /**
     * Scope untuk filter berdasarkan tipe kegiatan
     */
    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe_kegiatan', $tipe);
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Get riwayat vaksinasi
     */
    public static function getRiwayatVaksinasi($batchId)
    {
        return self::where('batch_produksi_id', $batchId)
            ->where('tipe_kegiatan', self::TIPE_VAKSINASI)
            ->orderBy('tanggal')
            ->get();
    }

    /**
     * Get total biaya kesehatan per batch
     */
    public static function getTotalBiayaKesehatan($batchId)
    {
        return self::where('batch_produksi_id', $batchId)
            ->sum('biaya');
    }

    /**
     * Hitung total burung yang sedang karantina (belum dikembalikan)
     */
    public static function totalKarantinaAktif($batchId)
    {
        return self::where('batch_produksi_id', $batchId)
            ->where('tipe_kegiatan', self::TIPE_KARANTINA)
            ->where('karantina_dikembalikan', false)
            ->sum('jumlah_burung');
    }

    /**
     * Jadwal vaksinasi standar untuk grower
     * Berdasarkan umur DOQ
     */
    public static function getJadwalVaksinasiStandar()
    {
        return [
            [
                'umur_hari' => 7,
                'range_hari' => [7, 10],
                'nama_vaksin' => 'ND (Newcastle Disease)',
                'metode' => 'Tetes mata/hidung',
                'keterangan' => 'Vaksinasi pertama untuk mencegah penyakit tetelo'
            ],
            [
                'umur_hari' => 14,
                'range_hari' => [14, 21],
                'nama_vaksin' => 'ND + IB (Infectious Bronchitis)',
                'metode' => 'Tetes/Spray',
                'keterangan' => 'Vaksinasi booster dan pencegahan bronchitis'
            ],
            [
                'umur_hari' => 28,
                'range_hari' => [28, 35],
                'nama_vaksin' => 'Fowl Pox',
                'metode' => 'Tusuk sayap',
                'keterangan' => 'Mencegah penyakit cacar unggas'
            ],
        ];
    }

    /**
     * Check apakah vaksinasi sudah dilakukan
     */
    public static function isVaksinasiSelesai($batchId, $namaVaksin)
    {
        return self::where('batch_produksi_id', $batchId)
            ->where('tipe_kegiatan', self::TIPE_VAKSINASI)
            ->where('nama_vaksin_obat', 'LIKE', "%{$namaVaksin}%")
            ->exists();
    }

    /**
     * Generate reminder vaksinasi
     * Berdasarkan umur batch dan jadwal standar
     */
    public static function generateReminder($batchId, $umurHari)
    {
        $jadwal = self::getJadwalVaksinasiStandar();
        $reminders = [];

        foreach ($jadwal as $item) {
            $isSelesai = self::isVaksinasiSelesai($batchId, $item['nama_vaksin']);
            
            // Cek apakah sudah waktunya
            $isWaktuVaksinasi = $umurHari >= $item['range_hari'][0] && 
                                $umurHari <= $item['range_hari'][1];
            
            // Cek apakah mendekati waktu vaksinasi (H-2)
            $isMendekati = $umurHari >= ($item['range_hari'][0] - 2) && 
                          $umurHari < $item['range_hari'][0];

            $status = 'pending';
            $badge = 'secondary';
            $message = '';

            if ($isSelesai) {
                $status = 'selesai';
                $badge = 'success';
                $message = '✅ Sudah dilakukan';
            } elseif ($isWaktuVaksinasi) {
                $status = 'harus_dilakukan';
                $badge = 'danger';
                $message = '⚠️ Harus dilakukan sekarang!';
            } elseif ($isMendekati) {
                $status = 'mendekati';
                $badge = 'warning';
                $message = '⏰ H-' . ($item['range_hari'][0] - $umurHari);
            } elseif ($umurHari < $item['range_hari'][0]) {
                $status = 'belum_waktunya';
                $badge = 'secondary';
                $message = '📅 Umur ' . $item['range_hari'][0] . ' hari';
            } else {
                $status = 'terlewat';
                $badge = 'dark';
                $message = '❌ Terlewat!';
            }

            $reminders[] = array_merge($item, [
                'status' => $status,
                'badge' => $badge,
                'message' => $message,
                'is_selesai' => $isSelesai,
            ]);
        }

        return $reminders;
    }

    /**
     * Get statistik kesehatan
     */
    public static function getStatistikKesehatan($batchId)
    {
        return self::where('batch_produksi_id', $batchId)
            ->selectRaw('
                tipe_kegiatan,
                COUNT(*) as jumlah_kegiatan,
                SUM(biaya) as total_biaya,
                SUM(jumlah_burung) as total_burung_ditangani
            ')
            ->groupBy('tipe_kegiatan')
            ->get();
    }
}
