<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StokPakan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vf_stok_pakan';

    protected $fillable = [
        'kode_pakan',
        'nama_pakan',
        'jenis_pakan',
        'merek',
        'harga_per_kg',
        'stok_kg',
        'stok_karung',
        'berat_per_karung',
        'tanggal_kadaluarsa',
        'supplier',
    ];

    protected $casts = [
        'harga_per_kg' => 'decimal:2',
        'stok_kg' => 'decimal:2',
        'berat_per_karung' => 'decimal:2',
        'tanggal_kadaluarsa' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship: Stok Pakan has many Pakan (Feed records)
     */
    public function pakan()
    {
        return $this->hasMany(Pakan::class, 'stok_pakan_id');
    }

    /**
     * Check if stock is low (below 100 kg)
     */
    public function isLowStock(): bool
    {
        return $this->stok_kg < 100;
    }

    /**
     * Check if feed is expired or near expiry (within 30 days)
     */
    public function isNearExpiry(): bool
    {
        if (!$this->tanggal_kadaluarsa) {
            return false;
        }
        
        $daysUntilExpiry = now()->diffInDays($this->tanggal_kadaluarsa, false);
        return $daysUntilExpiry <= 30;
    }

    /**
     * Check if feed is expired
     */
    public function isExpired(): bool
    {
        if (!$this->tanggal_kadaluarsa) {
            return false;
        }
        
        return $this->tanggal_kadaluarsa < now()->toDateString();
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        if ($this->isExpired()) {
            return 'Expired';
        }
        
        if ($this->isNearExpiry()) {
            return 'Near Expiry';
        }
        
        if ($this->isLowStock()) {
            return 'Low Stock';
        }
        
        if ($this->stok_kg == 0) {
            return 'Out of Stock';
        }
        
        return 'Available';
    }

    /**
     * Scope: Only available stock (not expired and has stock)
     */
    public function scopeAvailable($query)
    {
        return $query->where('stok_kg', '>', 0)
                     ->where(function($q) {
                         $q->whereNull('tanggal_kadaluarsa')
                           ->orWhere('tanggal_kadaluarsa', '>=', now()->toDateString());
                     });
    }

    /**
     * Scope: Low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->where('stok_kg', '<', 100)
                     ->where('stok_kg', '>', 0);
    }

    /**
     * Scope: Near expiry items (within 30 days)
     */
    public function scopeNearExpiry($query)
    {
        return $query->whereNotNull('tanggal_kadaluarsa')
                     ->whereBetween('tanggal_kadaluarsa', [
                         now()->toDateString(),
                         now()->addDays(30)->toDateString()
                     ]);
    }

    /**
     * Scope: Expired items
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('tanggal_kadaluarsa')
                     ->where('tanggal_kadaluarsa', '<', now()->toDateString());
    }
}
