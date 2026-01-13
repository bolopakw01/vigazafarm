<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Kandang extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        static::saved(function (Kandang $kandang) {
            $kandang->syncMaintenanceStatus();
        });
    }

    protected const TYPE_KEYWORDS = [
        'penetasan' => ['penetasan', 'tetasan', 'hatch', 'inkubasi', 'doc'],
        'pembesaran' => ['pembesaran', 'grow', 'grower', 'brooder', 'pullet', 'growout'],
        'produksi' => ['produksi', 'layer', 'petelur', 'breeding', 'indukan']
    ];

    protected const ACTIVE_STATUS_VALUES = ['aktif', 'active', 'berjalan', 'proses', 'ongoing', 'sedang berjalan'];

    protected ?string $usageTypeCache = null;
    protected ?int $penetasanUsageCache = null;
    protected ?int $pembesaranUsageCache = null;
    protected ?int $produksiUsageCache = null;

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
     * Status komputasi: jika kapasitas terpakai >= total maka dianggap "penuh".
     */
    public function getStatusComputedAttribute(): string
    {
        $baseStatus = strtolower(trim((string) ($this->status ?? '')));

        if ($this->isFull()) {
            return 'penuh';
        }

        return $baseStatus === '' ? 'tidak_aktif' : $baseStatus;
    }

    /**
     * True jika terpakai >= total kapasitas (mencegah penggunaan baru).
     */
    public function isFull(): bool
    {
        $total = $this->kapasitas_total;
        if ($total <= 0) {
            return false;
        }

        return ($this->kapasitas_terpakai ?? 0) >= $total;
    }

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
    public function getKapasitasTerpakaiAttribute(): int
    {
        $usageType = $this->resolveUsageType();

        return match ($usageType) {
            'penetasan' => $this->sumPenetasanUsage(),
            'pembesaran' => $this->sumPembesaranUsage(),
            'produksi' => $this->sumProduksiUsage(),
            default => max(
                $this->sumPenetasanUsage(),
                $this->sumPembesaranUsage(),
                $this->sumProduksiUsage()
            ),
        };
    }

    protected function resolveUsageType(): ?string
    {
        if ($this->usageTypeCache !== null) {
            return $this->usageTypeCache;
        }

        $rawType = strtolower(trim((string) ($this->tipe_kandang ?? $this->tipe ?? '')));

        if ($rawType !== '') {
            foreach (self::TYPE_KEYWORDS as $type => $keywords) {
                foreach ($keywords as $keyword) {
                    if ($keyword !== '' && str_contains($rawType, $keyword)) {
                        return $this->usageTypeCache = $type;
                    }
                }
            }
        }

        if ($this->relationItems('penetasan')->isNotEmpty()) {
            return $this->usageTypeCache = 'penetasan';
        }

        if ($this->relationItems('pembesaran')->isNotEmpty()) {
            return $this->usageTypeCache = 'pembesaran';
        }

        if ($this->relationItems('produksi')->isNotEmpty()) {
            return $this->usageTypeCache = 'produksi';
        }

        return $this->usageTypeCache = null;
    }

    protected function sumPenetasanUsage(): int
    {
        if ($this->penetasanUsageCache !== null) {
            return $this->penetasanUsageCache;
        }

        $total = $this->relationItems('penetasan')
            ->filter(fn ($penetasan) => $this->isActiveStatus($penetasan->status))
            ->sum(fn ($penetasan) => (int) ($penetasan->jumlah_telur ?? 0));

        return $this->penetasanUsageCache = (int) $total;
    }

    protected function sumPembesaranUsage(): int
    {
        if ($this->pembesaranUsageCache !== null) {
            return $this->pembesaranUsageCache;
        }

        $total = $this->relationItems('pembesaran')
            ->filter(fn ($batch) => $this->isActiveStatus($batch->status_batch))
            ->sum(function ($batch) {
                $jumlah = $batch->jumlah_anak_ayam ?? $batch->jumlah_siap ?? 0;
                return (int) $jumlah;
            });

        return $this->pembesaranUsageCache = (int) $total;
    }

    protected function sumProduksiUsage(): int
    {
        if ($this->produksiUsageCache !== null) {
            return $this->produksiUsageCache;
        }

        $total = $this->relationItems('produksi')
            ->filter(fn ($produksi) => $this->isActiveStatus($produksi->status))
            ->sum(function ($produksi) {
                $indukan = $produksi->jumlah_indukan;
                if ($indukan === null) {
                    $indukan = ($produksi->jumlah_betina ?? 0) + ($produksi->jumlah_jantan ?? 0);
                }

                return (int) $indukan;
            });

        return $this->produksiUsageCache = (int) $total;
    }

    /**
     * Kapasitas kandang yang teregistrasi (maksimal) dalam satuan ekor.
     */
    public function getKapasitasTotalAttribute(): int
    {
        $raw = $this->kapasitas_maksimal ?? $this->kapasitas ?? 0;

        return max((int) $raw, 0);
    }

    /**
     * Kapasitas tersisa (maksimal - terpakai) tanpa nilai negatif.
     */
    public function getKapasitasTersisaAttribute(): int
    {
        return max($this->kapasitas_total - $this->kapasitas_terpakai, 0);
    }

    protected function relationItems(string $relation): Collection
    {
        $this->loadMissing($relation);

        $value = $this->getRelationValue($relation);

        return $value instanceof Collection ? $value : collect();
    }

    protected function isActiveStatus(?string $status): bool
    {
        if ($status === null) {
            return false;
        }

        $normalized = strtolower(trim($status));

        return in_array($normalized, self::ACTIVE_STATUS_VALUES, true);
    }

    public function scopeTypeIs($query, string $type)
    {
        return $query->whereRaw('LOWER(COALESCE(tipe_kandang, "")) = ?', [strtolower($type)]);
    }

    public function scopeStatusEquals($query, string $status)
    {
        return $query->whereRaw('LOWER(COALESCE(status, "")) = ?', [strtolower($status)]);
    }

    public function scopeStatusIn($query, array $statuses)
    {
        $normalized = array_map(fn ($status) => strtolower($status), $statuses);

        return $query->whereIn(DB::raw('LOWER(COALESCE(status, ""))'), $normalized);
    }

    public function syncMaintenanceStatus(): void
    {
        $kapasitasTotal = $this->kapasitas_total;

        if ($kapasitasTotal <= 0) {
            return;
        }

        $kapasitasTerpakai = $this->kapasitas_terpakai;
        $currentStatus = strtolower((string) ($this->status ?? ''));

        if ($kapasitasTerpakai >= $kapasitasTotal && $currentStatus !== 'penuh') {
            $this->status = 'penuh';
            $this->saveQuietly();
        } elseif ($kapasitasTerpakai < $kapasitasTotal && $currentStatus === 'penuh') {
            $this->status = 'aktif';
            $this->saveQuietly();
        }
    }
}
