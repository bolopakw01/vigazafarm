<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SyncsKandangMaintenance;

class Penetasan extends Model
{
    use SyncsKandangMaintenance;
    protected $table = 'vf_penetasan';

    protected $fillable = [
        'batch',
        'kandang_id',
        'tanggal_simpan_telur',
        'estimasi_tanggal_menetas',
        'tanggal_masuk_hatcher',
        'jumlah_telur',
        'tanggal_menetas',
        'jumlah_menetas',
        'jumlah_doc',
        'suhu_penetasan',
        'kelembaban_penetasan',
        'telur_tidak_fertil',
        'persentase_tetas',
        'catatan',
        'status',
        'fase_penetasan',
        'doc_ditransfer',
        'telur_infertil_ditransfer',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_simpan_telur' => 'date',
        'estimasi_tanggal_menetas' => 'date',
        'tanggal_masuk_hatcher' => 'date',
        'tanggal_menetas' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    private const SETTER_DURATION_DAYS = 14;

    /**
     * Relasi ke tabel kandang
     */
    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    /**
     * Relasi ke pembesaran (one-to-many)
     */
    public function pembesaran()
    {
        return $this->hasMany(Pembesaran::class, 'penetasan_id');
    }

    /**
     * Relasi ke produksi (untuk telur infertil)
     */
    public function produksi()
    {
        return $this->hasMany(Produksi::class, 'penetasan_id');
    }

    /**
     * Relasi ke user yang membuat record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user yang terakhir mengupdate record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Hitung DOQ yang tersedia untuk ditransfer ke pembesaran
     */
    public function getDocTersediaAttribute()
    {
        return ($this->jumlah_doc ?? 0) - ($this->doc_ditransfer ?? 0);
    }

    /**
     * Hitung telur infertil yang tersedia untuk produksi
     */
    public function getTelurInfertilTersediaAttribute()
    {
        return ($this->telur_tidak_fertil ?? 0) - ($this->telur_infertil_ditransfer ?? 0);
    }

    /**
     * Target tanggal otomatis untuk memindahkan telur dari setter ke hatcher.
     */
    public function getTargetHatcherDateAttribute(): ?Carbon
    {
        if (empty($this->tanggal_simpan_telur)) {
            return null;
        }

        $start = $this->tanggal_simpan_telur instanceof Carbon
            ? $this->tanggal_simpan_telur->copy()
            : Carbon::parse($this->tanggal_simpan_telur);

        return $start->addDays(self::SETTER_DURATION_DAYS);
    }

    /**
     * Sinkronkan penetasan yang sudah melewati fase setter menjadi hatcher.
     */
    public static function syncHatcherTransitions(): int
    {
        $threshold = now()->subDays(self::SETTER_DURATION_DAYS)->startOfDay();
        $candidates = static::query()
            ->where('fase_penetasan', 'setter')
            ->whereNotNull('tanggal_simpan_telur')
            ->whereDate('tanggal_simpan_telur', '<=', $threshold)
            ->get();

        $updated = 0;

        foreach ($candidates as $penetasan) {
            $penetasan->fase_penetasan = 'hatcher';

            if (empty($penetasan->tanggal_masuk_hatcher)) {
                $start = $penetasan->tanggal_simpan_telur instanceof Carbon
                    ? $penetasan->tanggal_simpan_telur->copy()
                    : Carbon::parse($penetasan->tanggal_simpan_telur);

                $penetasan->tanggal_masuk_hatcher = $start->addDays(self::SETTER_DURATION_DAYS);
            }

            $penetasan->save();
            $updated++;
        }

        return $updated;
    }
}
