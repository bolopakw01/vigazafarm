<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KandangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $kandang = [
            [
                'kode_kandang' => 'KD-PEN-01',
                'nama_kandang' => 'Kandang Penetasan 1',
                'kapasitas_maksimal' => 5000,
                'tipe_kandang' => 'penetasan',
                'status' => 'aktif',
                'keterangan' => 'Kandang penetasan dengan inkubator otomatis',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kandang' => 'KD-PEN-02',
                'nama_kandang' => 'Kandang Penetasan 2',
                'kapasitas_maksimal' => 3000,
                'tipe_kandang' => 'penetasan',
                'status' => 'aktif',
                'keterangan' => 'Kandang penetasan cadangan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kandang' => 'KD-BES-01',
                'nama_kandang' => 'Kandang Pembesaran A',
                'kapasitas_maksimal' => 2000,
                'tipe_kandang' => 'pembesaran',
                'status' => 'aktif',
                'keterangan' => 'Kandang pembesaran DOC hingga siap produksi',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kandang' => 'KD-BES-02',
                'nama_kandang' => 'Kandang Pembesaran B',
                'kapasitas_maksimal' => 2000,
                'tipe_kandang' => 'pembesaran',
                'status' => 'aktif',
                'keterangan' => 'Kandang pembesaran dengan sistem otomatis',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kandang' => 'KD-PROD-01',
                'nama_kandang' => 'Kandang Produksi Layer 1',
                'kapasitas_maksimal' => 3000,
                'tipe_kandang' => 'produksi',
                'status' => 'aktif',
                'keterangan' => 'Kandang produksi utama dengan sistem baterai',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kandang' => 'KD-PROD-02',
                'nama_kandang' => 'Kandang Produksi Layer 2',
                'kapasitas_maksimal' => 3000,
                'tipe_kandang' => 'produksi',
                'status' => 'aktif',
                'keterangan' => 'Kandang produksi dengan ventilasi otomatis',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kandang' => 'KD-PROD-03',
                'nama_kandang' => 'Kandang Produksi Layer 3',
                'kapasitas_maksimal' => 2500,
                'tipe_kandang' => 'produksi',
                'status' => 'aktif',
                'keterangan' => 'Kandang produksi dengan sistem pencahayaan terprogram',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kandang' => 'KD-KAR-01',
                'nama_kandang' => 'Kandang Karantina',
                'kapasitas_maksimal' => 500,
                'tipe_kandang' => 'karantina',
                'status' => 'kosong',
                'keterangan' => 'Kandang untuk isolasi burung sakit atau karantina',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('kandang')->insert($kandang);
    }
}
