<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParameterStandarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Data standar untuk monitoring burung puyuh berdasarkan best practices
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $parameters = [
            // Parameter untuk fase DOC (0-14 hari)
            [
                'fase' => 'DOC',
                'parameter' => 'konsumsi_pakan_harian_per_ekor',
                'nilai_minimal' => 3.0,
                'nilai_optimal' => 5.0,
                'nilai_maksimal' => 7.0,
                'satuan' => 'gram',
                'keterangan' => 'Konsumsi pakan harian per ekor pada fase DOC',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'DOC',
                'parameter' => 'suhu_kandang',
                'nilai_minimal' => 32.0,
                'nilai_optimal' => 35.0,
                'nilai_maksimal' => 38.0,
                'satuan' => 'celsius',
                'keterangan' => 'Suhu optimal kandang untuk DOC',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'DOC',
                'parameter' => 'kelembaban',
                'nilai_minimal' => 60.0,
                'nilai_optimal' => 65.0,
                'nilai_maksimal' => 70.0,
                'satuan' => 'persen',
                'keterangan' => 'Kelembaban optimal untuk DOC',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'DOC',
                'parameter' => 'mortalitas',
                'nilai_minimal' => 0.0,
                'nilai_optimal' => 2.0,
                'nilai_maksimal' => 5.0,
                'satuan' => 'persen',
                'keterangan' => 'Target tingkat mortalitas pada fase DOC',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Parameter untuk fase Grower (15-35 hari)
            [
                'fase' => 'grower',
                'parameter' => 'konsumsi_pakan_harian_per_ekor',
                'nilai_minimal' => 10.0,
                'nilai_optimal' => 15.0,
                'nilai_maksimal' => 20.0,
                'satuan' => 'gram',
                'keterangan' => 'Konsumsi pakan harian per ekor pada fase grower',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'grower',
                'parameter' => 'berat_badan',
                'nilai_minimal' => 80.0,
                'nilai_optimal' => 100.0,
                'nilai_maksimal' => 120.0,
                'satuan' => 'gram',
                'keterangan' => 'Target berat badan pada akhir fase grower',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'grower',
                'parameter' => 'suhu_kandang',
                'nilai_minimal' => 25.0,
                'nilai_optimal' => 27.0,
                'nilai_maksimal' => 30.0,
                'satuan' => 'celsius',
                'keterangan' => 'Suhu optimal kandang untuk grower',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'grower',
                'parameter' => 'kelembaban',
                'nilai_minimal' => 55.0,
                'nilai_optimal' => 60.0,
                'nilai_maksimal' => 65.0,
                'satuan' => 'persen',
                'keterangan' => 'Kelembaban optimal untuk grower',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'grower',
                'parameter' => 'mortalitas',
                'nilai_minimal' => 0.0,
                'nilai_optimal' => 1.0,
                'nilai_maksimal' => 3.0,
                'satuan' => 'persen',
                'keterangan' => 'Target tingkat mortalitas pada fase grower',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Parameter untuk fase Layer (Produksi, 36+ hari)
            [
                'fase' => 'layer',
                'parameter' => 'konsumsi_pakan_harian_per_ekor',
                'nilai_minimal' => 18.0,
                'nilai_optimal' => 22.0,
                'nilai_maksimal' => 26.0,
                'satuan' => 'gram',
                'keterangan' => 'Konsumsi pakan harian per ekor pada fase layer',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'layer',
                'parameter' => 'produksi_telur_harian',
                'nilai_minimal' => 70.0,
                'nilai_optimal' => 85.0,
                'nilai_maksimal' => 95.0,
                'satuan' => 'persen',
                'keterangan' => 'Hen Day Production - persentase produksi telur harian',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'layer',
                'parameter' => 'berat_telur',
                'nilai_minimal' => 10.0,
                'nilai_optimal' => 12.0,
                'nilai_maksimal' => 14.0,
                'satuan' => 'gram',
                'keterangan' => 'Berat rata-rata telur yang diproduksi',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'layer',
                'parameter' => 'fcr',
                'nilai_minimal' => 2.0,
                'nilai_optimal' => 2.5,
                'nilai_maksimal' => 3.0,
                'satuan' => 'rasio',
                'keterangan' => 'Feed Conversion Ratio - efisiensi pakan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'layer',
                'parameter' => 'suhu_kandang',
                'nilai_minimal' => 20.0,
                'nilai_optimal' => 25.0,
                'nilai_maksimal' => 28.0,
                'satuan' => 'celsius',
                'keterangan' => 'Suhu optimal kandang untuk layer',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'layer',
                'parameter' => 'kelembaban',
                'nilai_minimal' => 50.0,
                'nilai_optimal' => 60.0,
                'nilai_maksimal' => 70.0,
                'satuan' => 'persen',
                'keterangan' => 'Kelembaban optimal untuk layer',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'layer',
                'parameter' => 'mortalitas',
                'nilai_minimal' => 0.0,
                'nilai_optimal' => 0.5,
                'nilai_maksimal' => 2.0,
                'satuan' => 'persen',
                'keterangan' => 'Target tingkat mortalitas bulanan pada fase layer',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'layer',
                'parameter' => 'telur_grade_a',
                'nilai_minimal' => 70.0,
                'nilai_optimal' => 85.0,
                'nilai_maksimal' => 100.0,
                'satuan' => 'persen',
                'keterangan' => 'Persentase telur grade A dari total produksi',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fase' => 'layer',
                'parameter' => 'intensitas_cahaya',
                'nilai_minimal' => 10.0,
                'nilai_optimal' => 15.0,
                'nilai_maksimal' => 20.0,
                'satuan' => 'lux',
                'keterangan' => 'Intensitas cahaya optimal untuk produksi telur',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('parameter_standar')->insert($parameters);
    }
}
