<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StokPakanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $stokPakan = [
            [
                'kode_pakan' => 'PKN-STR-001',
                'nama_pakan' => 'Pakan Starter BR-1',
                'jenis_pakan' => 'Starter',
                'merek' => 'Comfeed BR-1',
                'harga_per_kg' => 9500.00,
                'stok_kg' => 500.00,
                'stok_karung' => 10,
                'berat_per_karung' => 50.00,
                'tanggal_kadaluarsa' => Carbon::now()->addMonths(6),
                'supplier' => 'PT Charoen Pokphand',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_pakan' => 'PKN-GRW-001',
                'nama_pakan' => 'Pakan Grower 511',
                'jenis_pakan' => 'Grower',
                'merek' => 'Japfa Comfeed 511',
                'harga_per_kg' => 8500.00,
                'stok_kg' => 1000.00,
                'stok_karung' => 20,
                'berat_per_karung' => 50.00,
                'tanggal_kadaluarsa' => Carbon::now()->addMonths(5),
                'supplier' => 'PT Japfa Comfeed Indonesia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_pakan' => 'PKN-LAY-001',
                'nama_pakan' => 'Pakan Layer Hi-Pro-Vite 124',
                'jenis_pakan' => 'Layer',
                'merek' => 'Charoen Pokphand',
                'harga_per_kg' => 7800.00,
                'stok_kg' => 2000.00,
                'stok_karung' => 40,
                'berat_per_karung' => 50.00,
                'tanggal_kadaluarsa' => Carbon::now()->addMonths(4),
                'supplier' => 'PT Charoen Pokphand',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_pakan' => 'PKN-LAY-002',
                'nama_pakan' => 'Pakan Layer 124 BR',
                'jenis_pakan' => 'Layer',
                'merek' => 'Comfeed',
                'harga_per_kg' => 7500.00,
                'stok_kg' => 1500.00,
                'stok_karung' => 30,
                'berat_per_karung' => 50.00,
                'tanggal_kadaluarsa' => Carbon::now()->addMonths(4),
                'supplier' => 'PT Japfa Comfeed Indonesia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_pakan' => 'PKN-ORG-001',
                'nama_pakan' => 'Pakan Organik Premium',
                'jenis_pakan' => 'Layer',
                'merek' => 'Organik Nusantara',
                'harga_per_kg' => 12000.00,
                'stok_kg' => 250.00,
                'stok_karung' => 5,
                'berat_per_karung' => 50.00,
                'tanggal_kadaluarsa' => Carbon::now()->addMonths(3),
                'supplier' => 'CV Organik Nusantara',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('stok_pakan')->insert($stokPakan);
    }
}
