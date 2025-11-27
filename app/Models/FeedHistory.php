<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedHistory extends Model
{
    protected $table = 'vf_feed_histories';

    protected $fillable = [
        'batch_produksi_id',
        'stok_pakan_id',
        'feed_item_id',
        'tanggal',
        'jumlah_karung_sisa',
        'keterangan',
        'pengguna_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_karung_sisa' => 'integer',
    ];

    public function stokPakan(): BelongsTo
    {
        return $this->belongsTo(StokPakan::class, 'stok_pakan_id');
    }

    public function feedItem(): BelongsTo
    {
        return $this->belongsTo(FeedVitaminItem::class, 'feed_item_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}
