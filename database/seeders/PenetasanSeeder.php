<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Penetasan;
use Illuminate\Support\Carbon;

class PenetasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 15 dummy penetasan records spanning recent dates
        $start = Carbon::today()->subDays(30);
        $breeds = ['Layer A','Layer B','Broiler X','Broiler Y'];
        for ($i = 0; $i < 15; $i++) {
            $tanggal_simpan = $start->copy()->addDays($i * 2);
            $jumlah_telur = rand(80, 240);
            // simulate some hatching a week later
            $tanggal_menetas = $tanggal_simpan->copy()->addDays(7);
            $jumlah_menetas = (int) round($jumlah_telur * (rand(60, 90) / 100));
            $jumlah_doc = max(0, $jumlah_menetas - rand(0,5));

            Penetasan::create([
                'tanggal_simpan_telur' => $tanggal_simpan->toDateString(),
                'jumlah_telur' => $jumlah_telur,
                'tanggal_menetas' => $tanggal_menetas->toDateString(),
                'jumlah_menetas' => $jumlah_menetas,
                'jumlah_doc' => $jumlah_doc,
            ]);
        }
    }
}
