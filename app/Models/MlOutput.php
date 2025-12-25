<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlOutput extends Model
{
    use HasFactory;

    protected $table = 'vf_ml_outputs';

    protected $fillable = [
        'run_id',
        'type',
        'entity_type',
        'entity_id',
        'tanggal_prediksi',
        'horizon',
        'nilai',
        'lower',
        'upper',
        'score',
        'status_flag',
        'top_features',
        'meta',
    ];

    protected $casts = [
        'tanggal_prediksi' => 'date',
        'top_features' => 'array',
        'meta' => 'array',
    ];

    public function run()
    {
        return $this->belongsTo(MlRun::class, 'run_id');
    }
}
