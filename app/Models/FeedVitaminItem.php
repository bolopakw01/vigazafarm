<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedVitaminItem extends Model
{
    use HasFactory;

    protected $table = 'vf_feed_vitamin_items';

    protected $fillable = [
        'category',
        'name',
        'price',
        'unit',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
