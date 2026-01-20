<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PencatatanProduksi extends Model
{
    protected $table = 'vf_pencatatan_produksi';

    protected $fillable = [
        'produksi_id',
        'tanggal',
        'jumlah_produksi',
        'kualitas',
        'berat_rata_rata',
        'harga_per_unit',
        'catatan',
        'dibuat_oleh'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_produksi' => 'integer',
        'berat_rata_rata' => 'decimal:2',
        'harga_per_unit' => 'decimal:2'
    ];

    /**
     * Relasi ke produksi
     */
    public function produksi(): BelongsTo
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    /**
     * Relasi ke user yang membuat pencatatan
     */
    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Accessor untuk format tanggal Indonesia
     */
    public function getTanggalFormattedAttribute(): string
    {
        return $this->tanggal->format('d/m/Y');
    }

    /**
     * Accessor untuk total pendapatan
     */
    public function getTotalPendapatanAttribute(): float
    {
        return $this->jumlah_produksi * ($this->harga_per_unit ?? 0);
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopePeriode($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter berdasarkan kualitas
     */
    public function scopeKualitas($query, $kualitas)
    {
        return $query->where('kualitas', $kualitas);
    }
}
