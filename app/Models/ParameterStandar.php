<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParameterStandar extends Model
{
    use HasFactory;

    protected $table = 'parameter_standar';

    protected $fillable = [
        'fase',
        'parameter',
        'nilai_minimal',
        'nilai_optimal',
        'nilai_maksimal',
        'satuan',
        'keterangan',
    ];

    protected $casts = [
        'nilai_minimal' => 'decimal:2',
        'nilai_optimal' => 'decimal:2',
        'nilai_maksimal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Check if a value is within acceptable range
     */
    public function isWithinRange($nilai): bool
    {
        if ($this->nilai_minimal && $nilai < $this->nilai_minimal) {
            return false;
        }
        
        if ($this->nilai_maksimal && $nilai > $this->nilai_maksimal) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if a value is optimal
     */
    public function isOptimal($nilai): bool
    {
        if (!$this->nilai_optimal) {
            return $this->isWithinRange($nilai);
        }
        
        // Consider optimal if within ±10% of optimal value
        $tolerance = $this->nilai_optimal * 0.1;
        return abs($nilai - $this->nilai_optimal) <= $tolerance;
    }

    /**
     * Get status of a value
     */
    public function getStatus($nilai): string
    {
        if (!$this->isWithinRange($nilai)) {
            return 'kritis';
        }
        
        if ($this->isOptimal($nilai)) {
            return 'baik';
        }
        
        return 'perhatian';
    }

    /**
     * Get color class for status
     */
    public function getStatusColor($nilai): string
    {
        $status = $this->getStatus($nilai);
        
        return match($status) {
            'baik' => 'success',
            'perhatian' => 'warning',
            'kritis' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Scope: Get parameters for specific phase
     */
    public function scopeForFase($query, string $fase)
    {
        return $query->where('fase', $fase);
    }

    /**
     * Scope: Get parameter by name and phase
     */
    public function scopeByParameter($query, string $parameter, string $fase = null)
    {
        $query = $query->where('parameter', $parameter);
        
        if ($fase) {
            $query->where('fase', $fase);
        }
        
        return $query;
    }

    /**
     * Static helper: Get grower phase standards
     */
    public static function getGrowerStandards(): array
    {
        $standards = self::forFase('grower')->get();
        
        return $standards->keyBy('parameter')->toArray();
    }

    /**
     * Static helper: Get DOC phase standards
     */
    public static function getDocStandards(): array
    {
        $standards = self::forFase('DOC')->get();
        
        return $standards->keyBy('parameter')->toArray();
    }

    /**
     * Static helper: Get layer phase standards
     */
    public static function getLayerStandards(): array
    {
        $standards = self::forFase('layer')->get();
        
        return $standards->keyBy('parameter')->toArray();
    }

    /**
     * Static helper: Check if weight is within standard for phase
     */
    public static function checkWeight($berat, $fase): array
    {
        $standard = self::byParameter('berat', $fase)->first();
        
        if (!$standard) {
            return [
                'status' => 'unknown',
                'color' => 'secondary',
                'message' => 'Standar tidak tersedia'
            ];
        }
        
        return [
            'status' => $standard->getStatus($berat),
            'color' => $standard->getStatusColor($berat),
            'message' => self::getWeightMessage($berat, $standard),
            'standard' => $standard
        ];
    }

    /**
     * Static helper: Check if temperature is within standard
     */
    public static function checkTemperature($suhu, $fase): array
    {
        $standard = self::byParameter('suhu', $fase)->first();
        
        if (!$standard) {
            return [
                'status' => 'unknown',
                'color' => 'secondary',
                'message' => 'Standar tidak tersedia'
            ];
        }
        
        return [
            'status' => $standard->getStatus($suhu),
            'color' => $standard->getStatusColor($suhu),
            'message' => self::getTemperatureMessage($suhu, $standard),
            'standard' => $standard
        ];
    }

    /**
     * Static helper: Check if humidity is within standard
     */
    public static function checkHumidity($kelembaban, $fase): array
    {
        $standard = self::byParameter('kelembaban', $fase)->first();
        
        if (!$standard) {
            return [
                'status' => 'unknown',
                'color' => 'secondary',
                'message' => 'Standar tidak tersedia'
            ];
        }
        
        return [
            'status' => $standard->getStatus($kelembaban),
            'color' => $standard->getStatusColor($kelembaban),
            'message' => self::getHumidityMessage($kelembaban, $standard),
            'standard' => $standard
        ];
    }

    /**
     * Private helper: Get weight message
     */
    private static function getWeightMessage($berat, $standard): string
    {
        if ($berat < $standard->nilai_minimal) {
            return "Berat dibawah standar minimal ({$standard->nilai_minimal}{$standard->satuan})";
        }
        
        if ($berat > $standard->nilai_maksimal) {
            return "Berat diatas standar maksimal ({$standard->nilai_maksimal}{$standard->satuan})";
        }
        
        if ($standard->isOptimal($berat)) {
            return "Berat optimal ({$standard->nilai_optimal}{$standard->satuan})";
        }
        
        return "Berat dalam range normal";
    }

    /**
     * Private helper: Get temperature message
     */
    private static function getTemperatureMessage($suhu, $standard): string
    {
        if ($suhu < $standard->nilai_minimal) {
            return "Suhu terlalu rendah (min: {$standard->nilai_minimal}{$standard->satuan})";
        }
        
        if ($suhu > $standard->nilai_maksimal) {
            return "Suhu terlalu tinggi (max: {$standard->nilai_maksimal}{$standard->satuan})";
        }
        
        if ($standard->isOptimal($suhu)) {
            return "Suhu optimal ({$standard->nilai_optimal}{$standard->satuan})";
        }
        
        return "Suhu dalam range normal";
    }

    /**
     * Private helper: Get humidity message
     */
    private static function getHumidityMessage($kelembaban, $standard): string
    {
        if ($kelembaban < $standard->nilai_minimal) {
            return "Kelembaban terlalu rendah (min: {$standard->nilai_minimal}{$standard->satuan})";
        }
        
        if ($kelembaban > $standard->nilai_maksimal) {
            return "Kelembaban terlalu tinggi (max: {$standard->nilai_maksimal}{$standard->satuan})";
        }
        
        if ($standard->isOptimal($kelembaban)) {
            return "Kelembaban optimal ({$standard->nilai_optimal}{$standard->satuan})";
        }
        
        return "Kelembaban dalam range normal";
    }
}
