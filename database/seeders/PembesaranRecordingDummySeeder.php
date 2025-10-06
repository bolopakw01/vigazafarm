<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembesaranRecordingDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generate dummy data untuk testing visualization
     */
    public function run(): void
    {
        $kandangId = 3; // Kandang Pembesaran A (ID 3)
        $penggunaId = 1; // Pengguna ID 1
        $now = Carbon::now();
        
        // 1. CREATE BATCH PRODUKSI FIRST
        $batchId = DB::table('batch_produksi')->insertGetId([
            'kode_batch' => 'PB-DUMMY-' . $now->format('YmdHis'),
            'kandang_id' => $kandangId,
            'tanggal_mulai' => $now->copy()->subDays(7)->format('Y-m-d'),
            'tanggal_akhir' => null,
            'jumlah_awal' => 500,
            'jumlah_saat_ini' => 497, // 500 - 3 mati
            'fase' => 'DOC',
            'status' => 'aktif',
            'catatan' => 'Batch dummy untuk testing visualization pembesaran',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        // 2. CREATE PRODUKSI DATA
        $produksiId = DB::table('produksi')->insertGetId([
            'kandang_id' => $kandangId,
            'batch_produksi_id' => $batchId,
            'tanggal_mulai' => $now->copy()->subDays(7)->format('Y-m-d'),
            'jumlah_indukan' => 500,
            'umur_mulai_produksi' => 1, // day 1
            'tanggal_akhir' => null,
            'status' => 'aktif',
            'catatan' => 'Batch pembesaran DOC untuk testing visualization',
        ]);
        
        // 3. DATA PAKAN (7 hari terakhir)
        // Tabel pakan menggunakan produksi_id, bukan batch_produksi_id
        $pakanData = [];
        $stokPakanIds = [1, 2]; // Assuming stok_pakan with IDs 1 and 2 exist
        
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = $now->copy()->subDays($i)->format('Y-m-d');
            $jumlahKg = rand(15, 25) + (rand(0, 99) / 100); // 15-25 kg per hari
            $stokPakanId = $stokPakanIds[array_rand($stokPakanIds)];
            $hargaPerKg = $stokPakanId == 1 ? 9500 : 8500;
            
            $pakanData[] = [
                'produksi_id' => $produksiId,
                'stok_pakan_id' => $stokPakanId,
                'batch_produksi_id' => null,
                'tanggal' => $tanggal,
                'jumlah_kg' => $jumlahKg,
                'jumlah_karung' => 0,
                'harga_per_kg' => $hargaPerKg,
                'total_biaya' => $jumlahKg * $hargaPerKg,
            ];
            
            // Pakan sore
            $jumlahKg2 = rand(10, 18) + (rand(0, 99) / 100);
            $pakanData[] = [
                'produksi_id' => $produksiId,
                'stok_pakan_id' => $stokPakanId,
                'batch_produksi_id' => null,
                'tanggal' => $tanggal,
                'jumlah_kg' => $jumlahKg2,
                'jumlah_karung' => 0,
                'harga_per_kg' => $hargaPerKg,
                'total_biaya' => $jumlahKg2 * $hargaPerKg,
            ];
        }
        
        DB::table('pakan')->insert($pakanData);
        
        // 2. DATA KEMATIAN (occasional, not every day)
        // Tabel kematian menggunakan produksi_id, bukan batch_produksi_id
        $kematianData = [];
        $penyebabList = ['penyakit', 'stress', 'kecelakaan'];
        
        // Day 2: 1 death
        $kematianData[] = [
            'produksi_id' => $produksiId,
            'batch_produksi_id' => null,
            'tanggal' => $now->copy()->subDays(5)->format('Y-m-d'),
            'jumlah' => 1,
            'penyebab' => $penyebabList[array_rand($penyebabList)],
            'keterangan' => 'Terlihat lemas sejak kemarin',
        ];
        
        // Day 4: 2 deaths
        $kematianData[] = [
            'produksi_id' => $produksiId,
            'batch_produksi_id' => null,
            'tanggal' => $now->copy()->subDays(3)->format('Y-m-d'),
            'jumlah' => 2,
            'penyebab' => $penyebabList[array_rand($penyebabList)],
            'keterangan' => 'Kondisi kandang terlalu panas',
        ];
        
        DB::table('kematian')->insert($kematianData);
        
        // 3. DATA MONITORING LINGKUNGAN (3x sehari untuk 7 hari)
        $monitoringData = [];
        $waktuList = ['06:00:00', '12:00:00', '18:00:00'];
        $kondisiVentilasi = ['baik', 'cukup', 'kurang'];
        
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = $now->copy()->subDays($i)->format('Y-m-d');
            
            foreach ($waktuList as $waktu) {
                $hour = (int)substr($waktu, 0, 2);
                
                // Suhu variations based on time of day
                if ($hour == 6) {
                    $suhu = rand(240, 260) / 10; // 24-26°C morning
                } elseif ($hour == 12) {
                    $suhu = rand(270, 290) / 10; // 27-29°C noon
                } else {
                    $suhu = rand(250, 270) / 10; // 25-27°C evening
                }
                
                $monitoringData[] = [
                    'kandang_id' => $kandangId,
                    'batch_produksi_id' => null,
                    'waktu_pencatatan' => $tanggal . ' ' . $waktu,
                    'suhu' => $suhu,
                    'kelembaban' => rand(550, 650) / 10, // 55-65%
                    'intensitas_cahaya' => rand(200, 500), // lux
                    'kondisi_ventilasi' => $kondisiVentilasi[array_rand($kondisiVentilasi)],
                    'catatan' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        
        DB::table('monitoring_lingkungan')->insert($monitoringData);
        
        // 4. DATA LAPORAN HARIAN (7 hari)
        $laporanData = [];
        $cumulativeDeath = 0;
        
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = $now->copy()->subDays($i)->format('Y-m-d');
            
            // Calculate deaths for this day
            $deathsToday = 0;
            foreach ($kematianData as $kematian) {
                if ($kematian['tanggal'] == $tanggal) {
                    $deathsToday += $kematian['jumlah'];
                }
            }
            $cumulativeDeath += $deathsToday;
            
            // Calculate pakan for this day
            $pakanToday = 0;
            foreach ($pakanData as $pakan) {
                if ($pakan['tanggal'] == $tanggal) {
                    $pakanToday += $pakan['jumlah_kg'];
                }
            }
            
            $populasiAwal = 500; // from batch
            $populasiSaatIni = $populasiAwal - $cumulativeDeath;
            $mortalitas = ($cumulativeDeath / $populasiAwal) * 100;
            
            $laporanData[] = [
                'batch_produksi_id' => $batchId,
                'tanggal' => $tanggal,
                'jumlah_burung' => $populasiSaatIni,
                'produksi_telur' => 0, // DOC phase, no eggs yet
                'jumlah_kematian' => $deathsToday,
                'konsumsi_pakan_kg' => round($pakanToday, 2),
                'fcr' => null, // Not applicable for DOC phase
                'hen_day_production' => null,
                'mortalitas_kumulatif' => round($mortalitas, 2),
                'catatan_kejadian' => 'Kondisi ' . ($i % 3 == 0 ? 'sangat baik' : ($i % 2 == 0 ? 'baik' : 'normal')),
                'pengguna_id' => $penggunaId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        DB::table('laporan_harian')->insert($laporanData);
        
        // 5. DATA KESEHATAN (vaksinasi dan treatment)
        $kesehatanData = [];
        
        // Vaksinasi ND day 1
        $kesehatanData[] = [
            'batch_produksi_id' => $batchId,
            'tanggal' => $now->copy()->subDays(6)->format('Y-m-d'),
            'tipe_kegiatan' => 'vaksinasi',
            'nama_vaksin_obat' => 'Vaksin ND (Newcastle Disease)',
            'jumlah_burung' => 500,
            'gejala' => null,
            'diagnosa' => null,
            'tindakan' => 'Vaksinasi via air minum',
            'biaya' => 25000,
            'petugas' => 'Dr. Ahmad',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        // Vitamin day 3
        $kesehatanData[] = [
            'batch_produksi_id' => $batchId,
            'tanggal' => $now->copy()->subDays(4)->format('Y-m-d'),
            'tipe_kegiatan' => 'pengobatan',
            'nama_vaksin_obat' => 'Vitamin Stress',
            'jumlah_burung' => 500,
            'gejala' => 'Burung terlihat stress',
            'diagnosa' => 'Heat stress karena suhu tinggi',
            'tindakan' => 'Pemberian vitamin anti stress dan perbaikan ventilasi',
            'biaya' => 15000,
            'petugas' => 'Operator',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        // Pemeriksaan rutin day 5
        $kesehatanData[] = [
            'batch_produksi_id' => $batchId,
            'tanggal' => $now->copy()->subDays(2)->format('Y-m-d'),
            'tipe_kegiatan' => 'pemeriksaan_rutin',
            'nama_vaksin_obat' => null,
            'jumlah_burung' => 497,
            'gejala' => null,
            'diagnosa' => 'Kondisi sehat, pertumbuhan normal',
            'tindakan' => 'Pemeriksaan fisik dan sampling berat',
            'biaya' => 0,
            'petugas' => 'Dr. Budi',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        DB::table('kesehatan')->insert($kesehatanData);
        
        $this->command->info('✅ Dummy data berhasil di-generate!');
        $this->command->info('   - Batch ID: ' . $batchId);
        $this->command->info('   - Produksi ID: ' . $produksiId);
        $this->command->info('   - ' . count($pakanData) . ' data pakan');
        $this->command->info('   - ' . count($kematianData) . ' data kematian');
        $this->command->info('   - ' . count($monitoringData) . ' data monitoring lingkungan');
        $this->command->info('   - ' . count($laporanData) . ' data laporan harian');
        $this->command->info('   - ' . count($kesehatanData) . ' data kesehatan');
    }
}
