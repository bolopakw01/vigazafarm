<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdatePenetasanStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        // Update all existing penetasan records with appropriate status
        $penetasan = DB::table('penetasan')->get();
        
        foreach ($penetasan as $item) {
            $tanggalSimpan = Carbon::parse($item->tanggal_simpan_telur);
            $selisihHari = $now->diffInDays($tanggalSimpan);
            
            $status = 'proses'; // default
            
            // Jika sudah menetas (ada tanggal_menetas), status = selesai
            if ($item->tanggal_menetas) {
                $status = 'selesai';
            }
            // Jika sudah lebih dari 1 hari sejak input tapi belum menetas, status = aktif
            elseif ($selisihHari >= 1) {
                $status = 'aktif';
            }
            // Jika baru input hari ini, status = proses
            else {
                $status = 'proses';
            }
            
            DB::table('penetasan')
                ->where('id', $item->id)
                ->update(['status' => $status]);
        }
        
        echo "✅ Berhasil update status untuk " . count($penetasan) . " data penetasan\n";
    }
}
