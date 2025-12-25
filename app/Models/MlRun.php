<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlRun extends Model
{
    use HasFactory;

    protected $table = 'vf_ml_runs';

    protected $fillable = [
        'status',
        'label',
        'started_at',
        'finished_at',
        'error_message',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function outputs()
    {
        return $this->hasMany(MlOutput::class, 'run_id');
    }
}
