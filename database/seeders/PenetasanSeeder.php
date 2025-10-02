<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenetasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        // Get kandang penetasan
        $kandangIds = DB::table('kandang')
            ->where('tipe_kandang', 'penetasan')
            ->pluck('id')
            ->toArray();
        
        if (empty($kandangIds)) {
            echo "⚠️ Tidak ada kandang dengan tipe penetasan. Jalankan KandangSeeder terlebih dahulu.\n";
            return;
        }
        
        $penetasan = [];
        
        // Data penetasan batch 1 - Sudah selesai
        $penetasan[] = [
            'kandang_id' => $kandangIds[0],
            'tanggal_simpan_telur' => Carbon::now()->subDays(45)->format('Y-m-d'),
            'jumlah_telur' => 1000,
            'tanggal_menetas' => Carbon::now()->subDays(28)->format('Y-m-d'),
            'jumlah_menetas' => 850,
            'jumlah_doc' => 840,
            'suhu_penetasan' => 37.5,
            'kelembaban_penetasan' => 65.0,
            'telur_tidak_fertil' => 50,
            'persentase_tetas' => 85.0,
            'catatan' => 'Penetasan berhasil dengan baik, kondisi optimal',
            'dibuat_pada' => Carbon::now()->subDays(45),
            'diperbarui_pada' => Carbon::now()->subDays(28),
        ];
        
        // Data penetasan batch 2 - Sudah selesai
        $penetasan[] = [
            'kandang_id' => $kandangIds[0],
            'tanggal_simpan_telur' => Carbon::now()->subDays(35)->format('Y-m-d'),
            'jumlah_telur' => 800,
            'tanggal_menetas' => Carbon::now()->subDays(18)->format('Y-m-d'),
            'jumlah_menetas' => 720,
            'jumlah_doc' => 710,
            'suhu_penetasan' => 37.8,
            'kelembaban_penetasan' => 63.0,
            'telur_tidak_fertil' => 30,
            'persentase_tetas' => 90.0,
            'catatan' => 'Persentase tetas sangat baik',
            'dibuat_pada' => Carbon::now()->subDays(35),
            'diperbarui_pada' => Carbon::now()->subDays(18),
        ];
        
        // Data penetasan batch 3 - Dalam proses
        $penetasan[] = [
            'kandang_id' => $kandangIds[1] ?? $kandangIds[0],
            'tanggal_simpan_telur' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'jumlah_telur' => 1200,
            'tanggal_menetas' => null,
            'jumlah_menetas' => null,
            'jumlah_doc' => null,
            'suhu_penetasan' => 37.6,
            'kelembaban_penetasan' => 64.0,
            'telur_tidak_fertil' => null,
            'persentase_tetas' => null,
            'catatan' => 'Masih dalam proses penetasan, suhu dan kelembaban terjaga',
            'dibuat_pada' => Carbon::now()->subDays(10),
            'diperbarui_pada' => Carbon::now()->subDays(10),
        ];
        
        // Data penetasan batch 4 - Hasil kurang optimal
        $penetasan[] = [
            'kandang_id' => $kandangIds[0],
            'tanggal_simpan_telur' => Carbon::now()->subDays(50)->format('Y-m-d'),
            'jumlah_telur' => 900,
            'tanggal_menetas' => Carbon::now()->subDays(33)->format('Y-m-d'),
            'jumlah_menetas' => 630,
            'jumlah_doc' => 620,
            'suhu_penetasan' => 38.2,
            'kelembaban_penetasan' => 70.0,
            'telur_tidak_fertil' => 120,
            'persentase_tetas' => 70.0,
            'catatan' => 'Persentase tetas di bawah target, kemungkinan suhu terlalu tinggi',
            'dibuat_pada' => Carbon::now()->subDays(50),
            'diperbarui_pada' => Carbon::now()->subDays(33),
        ];
        
        // Data penetasan batch 5 - Hasil sangat baik
        $penetasan[] = [
            'kandang_id' => $kandangIds[1] ?? $kandangIds[0],
            'tanggal_simpan_telur' => Carbon::now()->subDays(40)->format('Y-m-d'),
            'jumlah_telur' => 1500,
            'tanggal_menetas' => Carbon::now()->subDays(23)->format('Y-m-d'),
            'jumlah_menetas' => 1425,
            'jumlah_doc' => 1410,
            'suhu_penetasan' => 37.5,
            'kelembaban_penetasan' => 65.0,
            'telur_tidak_fertil' => 25,
            'persentase_tetas' => 95.0,
            'catatan' => 'Penetasan sempurna! Kondisi optimal sepanjang proses',
            'dibuat_pada' => Carbon::now()->subDays(40),
            'diperbarui_pada' => Carbon::now()->subDays(23),
        ];
        
        // Data penetasan batch 6 - Dalam proses
        $penetasan[] = [
            'kandang_id' => $kandangIds[0],
            'tanggal_simpan_telur' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'jumlah_telur' => 950,
            'tanggal_menetas' => null,
            'jumlah_menetas' => null,
            'jumlah_doc' => null,
            'suhu_penetasan' => 37.4,
            'kelembaban_penetasan' => 66.0,
            'telur_tidak_fertil' => null,
            'persentase_tetas' => null,
            'catatan' => 'Baru dimulai, monitoring ketat dilakukan',
            'dibuat_pada' => Carbon::now()->subDays(5),
            'diperbarui_pada' => Carbon::now()->subDays(5),
        ];
        
        // Data penetasan batch 7 - Hasil sedang
        $penetasan[] = [
            'kandang_id' => $kandangIds[1] ?? $kandangIds[0],
            'tanggal_simpan_telur' => Carbon::now()->subDays(55)->format('Y-m-d'),
            'jumlah_telur' => 1100,
            'tanggal_menetas' => Carbon::now()->subDays(38)->format('Y-m-d'),
            'jumlah_menetas' => 880,
            'jumlah_doc' => 870,
            'suhu_penetasan' => 37.7,
            'kelembaban_penetasan' => 62.0,
            'telur_tidak_fertil' => 80,
            'persentase_tetas' => 80.0,
            'catatan' => 'Hasil sesuai target standar',
            'dibuat_pada' => Carbon::now()->subDays(55),
            'diperbarui_pada' => Carbon::now()->subDays(38),
        ];
        
        // Data penetasan batch 8 - Baru saja selesai
        $penetasan[] = [
            'kandang_id' => $kandangIds[0],
            'tanggal_simpan_telur' => Carbon::now()->subDays(20)->format('Y-m-d'),
            'jumlah_telur' => 1300,
            'tanggal_menetas' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'jumlah_menetas' => 1105,
            'jumlah_doc' => 1095,
            'suhu_penetasan' => 37.6,
            'kelembaban_penetasan' => 64.5,
            'telur_tidak_fertil' => 45,
            'persentase_tetas' => 85.0,
            'catatan' => 'Baru selesai menetas, DOC dalam kondisi baik',
            'dibuat_pada' => Carbon::now()->subDays(20),
            'diperbarui_pada' => Carbon::now()->subDays(3),
        ];
        
        DB::table('penetasan')->insert($penetasan);
        
        echo "✅ Berhasil menambahkan " . count($penetasan) . " data penetasan\n";
    }
}
