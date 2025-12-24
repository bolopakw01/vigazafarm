<?php

namespace Database\Seeders;

use App\Models\Kandang;
use App\Models\BatchProduksi;
use App\Models\Pembesaran;
use App\Models\Penetasan;
use App\Models\Produksi;
use App\Models\LaporanHarian;
use App\Models\PencatatanProduksi;
use App\Models\BeratSampling;
use App\Models\Pakan;
use App\Models\Kematian;
use App\Models\MonitoringLingkungan;
use App\Models\Kesehatan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyFarmDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();

            // Bersihkan data dummy sebelumnya secara berurutan untuk hindari FK conflicts
            $kandangCodes = ['KDG-HTC-01', 'KDG-GRW-01', 'KDG-LYR-01', 'KDG-PRD-02', 'KDG-KAR-01'];
            $existingKandangIds = Kandang::withTrashed()->whereIn('kode_kandang', $kandangCodes)->pluck('id');

            if ($existingKandangIds->isNotEmpty()) {
                $batchIds = DB::table('vf_batch_produksi')->whereIn('kandang_id', $existingKandangIds)->pluck('id');

                if ($batchIds->isNotEmpty()) {
                    DB::table('vf_laporan_harian')->whereIn('batch_produksi_id', $batchIds)->delete();
                    DB::table('vf_pencatatan_produksi')->whereIn('produksi_id', function ($q) use ($batchIds) {
                        $q->select('id')->from('vf_produksi')->whereIn('batch_produksi_id', $batchIds);
                    })->delete();
                    DB::table('vf_pakan')->whereIn('batch_produksi_id', $batchIds)->orWhereIn('produksi_id', function ($q) use ($batchIds) {
                        $q->select('id')->from('vf_produksi')->whereIn('batch_produksi_id', $batchIds);
                    })->delete();
                    DB::table('vf_kematian')->whereIn('batch_produksi_id', $batchIds)->delete();
                    DB::table('vf_monitoring_lingkungan')->whereIn('batch_produksi_id', $batchIds)->delete();
                    DB::table('vf_kesehatan')->whereIn('batch_produksi_id', $batchIds)->delete();
                    DB::table('vf_berat_sampling')->whereIn('batch_produksi_id', $batchIds)->delete();
                    DB::table('vf_produksi')->whereIn('batch_produksi_id', $batchIds)->delete();
                    DB::table('vf_pembesaran')->whereIn('batch_produksi_id', $batchIds)->delete();
                    DB::table('vf_batch_produksi')->whereIn('id', $batchIds)->delete();
                }

                // Penetasan yang terhubung ke kandang dummy
                DB::table('vf_penetasan')->whereIn('kandang_id', $existingKandangIds)->delete();

                // Hapus kandang dummy (hard delete)
                Kandang::withTrashed()->whereIn('id', $existingKandangIds)->forceDelete();
            }

            $kandangPenetasan = Kandang::updateOrCreate(
                ['kode_kandang' => 'KDG-HTC-01'],
                [
                    'nama_kandang' => 'Kandang Penetasan Utama',
                    'kapasitas_maksimal' => 2000,
                    'tipe_kandang' => 'penetasan',
                    'status' => 'aktif',
                    'keterangan' => 'Dummy data untuk penetasan'
                ]
            );

            $kandangPembesaran = Kandang::updateOrCreate(
                ['kode_kandang' => 'KDG-GRW-01'],
                [
                    'nama_kandang' => 'Grower 1',
                    'kapasitas_maksimal' => 1500,
                    'tipe_kandang' => 'pembesaran',
                    'status' => 'aktif',
                    'keterangan' => 'Dummy data untuk pembesaran'
                ]
            );

            $kandangKarantina = Kandang::updateOrCreate(
                ['kode_kandang' => 'KDG-KAR-01'],
                [
                    'nama_kandang' => 'Karantina 1',
                    'kapasitas_maksimal' => 300,
                    'tipe_kandang' => 'karantina',
                    'status' => 'kosong',
                    'keterangan' => 'Dummy kandang karantina non-aktif'
                ]
            );

            $kandangProduksi = Kandang::updateOrCreate(
                ['kode_kandang' => 'KDG-LYR-01'],
                [
                    'nama_kandang' => 'Layer 1',
                    'kapasitas_maksimal' => 1200,
                    'tipe_kandang' => 'produksi',
                    'status' => 'aktif',
                    'keterangan' => 'Dummy data untuk produksi'
                ]
            );

            $kandangProduksi2 = Kandang::updateOrCreate(
                ['kode_kandang' => 'KDG-PRD-02'],
                [
                    'nama_kandang' => 'Produksi Puyuh 2',
                    'kapasitas_maksimal' => 900,
                    'tipe_kandang' => 'produksi',
                    'status' => 'kosong', // enum: aktif|maintenance|kosong
                    'keterangan' => 'Dummy data produksi puyuh (non-aktif)'
                ]
            );

            $batchLayer = BatchProduksi::updateOrCreate(
                ['kode_batch' => 'BATCH-LYR-001'],
                [
                    'kandang_id' => $kandangProduksi->id,
                    'tanggal_mulai' => $now->copy()->subDays(90)->toDateString(),
                    'tanggal_akhir' => null,
                    'jumlah_awal' => 520,
                    'jumlah_saat_ini' => 500,
                    'fase' => 'layer',
                    'status' => 'aktif',
                    'catatan' => 'Batch layer dummy untuk demo DSS.'
                ]
            );

            $batchGrowJantan = BatchProduksi::updateOrCreate(
                ['kode_batch' => 'BATCH-GRW-002'],
                [
                    'kandang_id' => $kandangPembesaran->id,
                    'tanggal_mulai' => $now->copy()->subDays(18)->toDateString(),
                    'tanggal_akhir' => null,
                    'jumlah_awal' => 400,
                    'jumlah_saat_ini' => 395,
                    'fase' => 'grower',
                    'status' => 'selesai',
                    'catatan' => 'Batch grower jantan dummy.'
                ]
            );

            $batchGrowCampur = BatchProduksi::updateOrCreate(
                ['kode_batch' => 'BATCH-GRW-003'],
                [
                    'kandang_id' => $kandangPembesaran->id,
                    'tanggal_mulai' => $now->copy()->subDays(12)->toDateString(),
                    'tanggal_akhir' => null,
                    'jumlah_awal' => 350,
                    'jumlah_saat_ini' => 348,
                    'fase' => 'grower',
                    'status' => 'selesai',
                    'catatan' => 'Batch grower campuran dummy.'
                ]
            );

            $penetasan = Penetasan::updateOrCreate(
                ['batch' => 'BATCH-HTS-001'],
                [
                    'kandang_id' => $kandangPenetasan->id,
                    'tanggal_simpan_telur' => $now->copy()->subDays(15)->toDateString(),
                    'estimasi_tanggal_menetas' => $now->copy()->subDays(15)->addDays(17)->toDateString(),
                    'tanggal_masuk_hatcher' => $now->copy()->subDays(15)->addDays(14)->toDateString(),
                    'jumlah_telur' => 1200,
                    'tanggal_menetas' => $now->copy()->subDays(1)->toDateString(),
                    'jumlah_menetas' => 1100,
                    'jumlah_doc' => 1080,
                    'suhu_penetasan' => 37.6,
                    'kelembaban_penetasan' => 55.5,
                    'telur_tidak_fertil' => 70,
                    'persentase_tetas' => 91.7,
                    'catatan' => 'Batch dummy untuk demo DSS.',
                    'status' => 'proses',
                    'fase_penetasan' => 'hatcher',
                    'doc_ditransfer' => 600,
                    'telur_infertil_ditransfer' => 30,
                ]
            );

            Penetasan::updateOrCreate(
                ['batch' => 'BATCH-HTS-002'],
                [
                    'kandang_id' => $kandangPenetasan->id,
                    'tanggal_simpan_telur' => $now->copy()->subDays(30)->toDateString(),
                    'estimasi_tanggal_menetas' => $now->copy()->subDays(30)->addDays(17)->toDateString(),
                    'tanggal_masuk_hatcher' => $now->copy()->subDays(30)->addDays(14)->toDateString(),
                    'jumlah_telur' => 900,
                    'tanggal_menetas' => $now->copy()->subDays(12)->toDateString(),
                    'jumlah_menetas' => 830,
                    'jumlah_doc' => 820,
                    'suhu_penetasan' => 37.5,
                    'kelembaban_penetasan' => 55.0,
                    'telur_tidak_fertil' => 40,
                    'persentase_tetas' => 92.2,
                    'catatan' => 'Batch selesai untuk referensi status.',
                    'status' => 'selesai',
                    'fase_penetasan' => 'hatcher',
                    'doc_ditransfer' => 800,
                    'telur_infertil_ditransfer' => 20,
                ]
            );

            Penetasan::updateOrCreate(
                ['batch' => 'BATCH-HTS-003'],
                [
                    'kandang_id' => $kandangPenetasan->id,
                    'tanggal_simpan_telur' => $now->copy()->subDays(10)->toDateString(),
                    'estimasi_tanggal_menetas' => $now->copy()->addDays(7)->toDateString(),
                    'tanggal_masuk_hatcher' => $now->copy()->addDays(4)->toDateString(),
                    'jumlah_telur' => 700,
                    'tanggal_menetas' => null,
                    'jumlah_menetas' => null,
                    'jumlah_doc' => null,
                    'suhu_penetasan' => 37.4,
                    'kelembaban_penetasan' => 54.0,
                    'telur_tidak_fertil' => 0,
                    'persentase_tetas' => null,
                    'catatan' => 'Batch gagal sebagai contoh status.',
                    'status' => 'gagal',
                    'fase_penetasan' => 'setter',
                    'doc_ditransfer' => 0,
                    'telur_infertil_ditransfer' => 0,
                ]
            );

            $pembesaran = Pembesaran::updateOrCreate(
                [
                    'kandang_id' => $kandangPembesaran->id,
                    'tanggal_masuk' => $now->copy()->subDays(20)->toDateString(),
                ],
                [
                    'batch_produksi_id' => $batchLayer->id,
                    'penetasan_id' => $penetasan->id,
                    'jumlah_anak_ayam' => 120,
                    'jenis_kelamin' => 'betina',
                    'status_batch' => 'Aktif',
                    'tanggal_selesai' => null,
                    'tanggal_siap' => $now->copy()->addDays(7)->toDateString(),
                    'jumlah_siap' => 115,
                    'umur_hari' => 5,
                    'berat_rata_rata' => 85,
                    'target_berat_akhir' => 120,
                    'kondisi_doc' => 'Baik',
                    'catatan' => 'Dummy batch grower aktif minimal.',
                    'indukan_ditransfer' => 0,
                ]
            );

            $pembesaranJantan = Pembesaran::updateOrCreate(
                [
                    'kandang_id' => $kandangPembesaran->id,
                    'tanggal_masuk' => $now->copy()->subDays(18)->toDateString(),
                    'jenis_kelamin' => 'jantan',
                ],
                [
                    'batch_produksi_id' => $batchGrowJantan->id,
                    'penetasan_id' => $penetasan->id,
                    'jumlah_anak_ayam' => 400,
                    'status_batch' => 'selesai',
                    'tanggal_selesai' => null,
                    'tanggal_siap' => $now->copy()->addDays(18)->toDateString(),
                    'jumlah_siap' => 395,
                    'umur_hari' => 18,
                    'berat_rata_rata' => 130,
                    'target_berat_akhir' => 180,
                    'kondisi_doc' => 'Baik',
                    'catatan' => 'Batch grower jantan dummy.',
                    'indukan_ditransfer' => 0,
                ]
            );

            $pembesaranCampur = Pembesaran::updateOrCreate(
                [
                    'kandang_id' => $kandangPembesaran->id,
                    'tanggal_masuk' => $now->copy()->subDays(12)->toDateString(),
                    'jenis_kelamin' => 'campuran',
                ],
                [
                    'batch_produksi_id' => $batchGrowCampur->id,
                    'penetasan_id' => $penetasan->id,
                    'jumlah_anak_ayam' => 350,
                    'status_batch' => 'selesai',
                    'tanggal_selesai' => null,
                    'tanggal_siap' => $now->copy()->addDays(22)->toDateString(),
                    'jumlah_siap' => 348,
                    'umur_hari' => 12,
                    'berat_rata_rata' => 105,
                    'target_berat_akhir' => 170,
                    'kondisi_doc' => 'Baik',
                    'catatan' => 'Batch grower campuran dummy.',
                    'indukan_ditransfer' => 0,
                ]
            );

            $produksi = Produksi::updateOrCreate(
                [
                    'kandang_id' => $kandangProduksi->id,
                    'tanggal_mulai' => $now->copy()->subDays(90)->toDateString(),
                ],
                [
                    'batch_produksi_id' => $batchLayer->id,
                    'penetasan_id' => $penetasan->id,
                    'pembesaran_id' => $pembesaran->id,
                    'produksi_sumber_id' => null,
                    'tipe_produksi' => 'telur',
                    'jenis_input' => 'dari_pembesaran',
                    'tanggal_akhir' => null,
                    'jumlah_telur' => 480,
                    'jumlah_indukan' => 500,
                    'jumlah_jantan' => 50,
                    'jumlah_betina' => 450,
                    'umur_mulai_produksi' => 60,
                    'berat_rata_rata' => 165,
                    'berat_rata_telur' => 12.4,
                    'persentase_fertil' => 82.5,
                    'harga_per_pcs' => 450,
                    'harga_per_kg' => null,
                    'status' => 'aktif',
                    'catatan' => 'Dummy data produksi layer, volume tidak melebihi populasi.',
                ]
            );

            $batchPuyuh = BatchProduksi::updateOrCreate(
                ['kode_batch' => 'BATCH-PUY-001'],
                [
                    'kandang_id' => $kandangProduksi2->id,
                    'tanggal_mulai' => $now->copy()->subDays(60)->toDateString(),
                    'tanggal_akhir' => null,
                    'jumlah_awal' => 320,
                    'jumlah_saat_ini' => 310,
                    'fase' => 'layer',
                    'status' => 'selesai',
                    'catatan' => 'Batch puyuh non-aktif.',
                ]
            );

            $produksiPuyuh = Produksi::updateOrCreate(
                [
                    'kandang_id' => $kandangProduksi2->id,
                    'tanggal_mulai' => $now->copy()->subDays(60)->toDateString(),
                ],
                [
                    'batch_produksi_id' => $batchPuyuh->id,
                    'penetasan_id' => $penetasan->id,
                    'pembesaran_id' => $pembesaranCampur->id,
                    'produksi_sumber_id' => null,
                    'tipe_produksi' => 'puyuh',
                    'jenis_input' => 'manual',
                    'tanggal_akhir' => null,
                    'jumlah_telur' => 0,
                    'jumlah_indukan' => 310,
                    'jumlah_jantan' => 60,
                    'jumlah_betina' => 250,
                    'umur_mulai_produksi' => 50,
                    'berat_rata_rata' => 180,
                    'berat_rata_telur' => 11.5,
                    'persentase_fertil' => 78.5,
                    'harga_per_pcs' => 380,
                    'harga_per_kg' => null,
                    'status' => 'tidak_aktif',
                    'catatan' => 'Dummy produksi puyuh tidak aktif.',
                ]
            );

            // Laporan harian ringkas (1 hari) untuk batch aktif layer
            $tanggal = $now->toDateString();
            LaporanHarian::updateOrCreate(
                [
                    'batch_produksi_id' => $batchLayer->id,
                    'tanggal' => $tanggal,
                ],
                [
                    'jumlah_burung' => 115,
                    'produksi_telur' => 1000,
                    'jumlah_kematian' => 0,
                    'konsumsi_pakan_kg' => 8,
                    'sisa_pakan_kg' => 2,
                    'harga_pakan_per_kg' => 8500,
                    'biaya_pakan_harian' => 8 * 8500,
                    'fcr' => 2.4,
                    'hen_day_production' => 90,
                    'mortalitas_kumulatif' => 0,
                    'tampilkan_di_histori' => true,
                    'pengguna_id' => 1,
                ]
            );

            // Batch puyuh non-aktif - tidak menambah laporan harian

            // Batch jantan & campur non-aktif - tidak menambah laporan harian

            // Sampling berat untuk pembesaran
            foreach ([7 => 95, 14 => 125, 20 => 150] as $umur => $berat) {
                BeratSampling::updateOrCreate(
                    [
                        'batch_produksi_id' => $batchLayer->id,
                        'umur_hari' => $umur,
                    ],
                    [
                        'tanggal_sampling' => $now->copy()->subDays(20 - $umur)->toDateString(),
                        'berat_rata_rata' => $berat,
                        'jumlah_sampel' => 30,
                        'catatan' => 'Sampling dummy untuk grafik pembesaran.',
                        'pengguna_id' => 1,
                    ]
                );
            }

            // Sampling berat batch jantan
            foreach ([10 => 140, 18 => 175] as $umur => $berat) {
                BeratSampling::updateOrCreate(
                    [
                        'batch_produksi_id' => $batchGrowJantan->id,
                        'umur_hari' => $umur,
                    ],
                    [
                        'tanggal_sampling' => $now->copy()->subDays(18 - $umur)->toDateString(),
                        'berat_rata_rata' => $berat,
                        'jumlah_sampel' => 25,
                        'catatan' => 'Sampling batch jantan.',
                        'pengguna_id' => 1,
                    ]
                );
            }

            // Sampling berat batch campuran
            foreach ([8 => 110, 12 => 135] as $umur => $berat) {
                BeratSampling::updateOrCreate(
                    [
                        'batch_produksi_id' => $batchGrowCampur->id,
                        'umur_hari' => $umur,
                    ],
                    [
                        'tanggal_sampling' => $now->copy()->subDays(12 - $umur)->toDateString(),
                        'berat_rata_rata' => $berat,
                        'jumlah_sampel' => 20,
                        'catatan' => 'Sampling batch campuran.',
                        'pengguna_id' => 1,
                    ]
                );
            }

            // Pencatatan produksi harian untuk grafik produksi
            DB::table('vf_pencatatan_produksi')->updateOrInsert(
                [
                    'produksi_id' => $produksi->id,
                    'tanggal' => $now->toDateString(),
                ],
                [
                    'jumlah_produksi' => 1000,
                    'kualitas' => 'baik',
                    'berat_rata_rata' => 12.0,
                    'harga_per_unit' => 500,
                    'catatan' => 'Dummy produksi aktif tunggal (500k).',
                    'dibuat_oleh' => 1,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );


            // Produksi puyuh non-aktif - tidak menambah catatan produksi
            // Pakan (grower) untuk tiga batch
            $feedPlans = [
                $batchLayer->id => [
                    ['hari' => 1, 'kg' => 8.0, 'harga' => 8500, 'sisa' => 2.0],
                ],
            ];

            foreach ($feedPlans as $batchId => $entries) {
                foreach ($entries as $entry) {
                    Pakan::updateOrCreate(
                        [
                            'batch_produksi_id' => $batchId,
                            'tanggal' => $now->copy()->subDays($entry['hari'])->toDateString(),
                        ],
                        [
                            'jumlah_kg' => $entry['kg'],
                            'sisa_pakan_kg' => $entry['sisa'],
                            'jumlah_karung' => null,
                            'harga_per_kg' => $entry['harga'],
                            'total_biaya' => $entry['kg'] * $entry['harga'],
                            'pengguna_id' => 1,
                        ]
                    );
                }
            }

            // Kematian per batch (kecil supaya tidak over)
            $deathPlans = [
                ['batch' => $batchLayer->id, 'hari' => 3, 'jumlah' => 2],
                ['batch' => $batchPuyuh->id, 'hari' => 4, 'jumlah' => 1],
                ['batch' => $batchGrowJantan->id, 'hari' => 1, 'jumlah' => 1],
                ['batch' => $batchGrowCampur->id, 'hari' => 2, 'jumlah' => 1],
            ];

            foreach ($deathPlans as $plan) {
                Kematian::updateOrCreate(
                    [
                        'batch_produksi_id' => $plan['batch'],
                        'tanggal' => $now->copy()->subDays($plan['hari'])->toDateString(),
                    ],
                    [
                        'jumlah' => $plan['jumlah'],
                        'penyebab' => Kematian::PENYEBAB_PENYAKIT,
                        'keterangan' => 'Dummy kematian untuk pencatatan pembesaran.',
                        'pengguna_id' => 1,
                    ]
                );
            }

            // Monitoring lingkungan (per batch) dengan variasi suhu/kelembaban
            $monitorPlans = [
                $batchLayer->id => [
                    ['jam' => 6, 'suhu' => 26.5, 'hum' => 58, 'lux' => 120, 'vent' => MonitoringLingkungan::VENTILASI_BAIK],
                    ['jam' => 14, 'suhu' => 27.1, 'hum' => 60, 'lux' => 135, 'vent' => MonitoringLingkungan::VENTILASI_BAIK],
                ],
                $batchPuyuh->id => [
                    ['jam' => 7, 'suhu' => 26.8, 'hum' => 59, 'lux' => 122, 'vent' => MonitoringLingkungan::VENTILASI_BAIK],
                    ['jam' => 15, 'suhu' => 27.3, 'hum' => 60, 'lux' => 138, 'vent' => MonitoringLingkungan::VENTILASI_BAIK],
                ],
                $batchGrowJantan->id => [
                    ['jam' => 7, 'suhu' => 27.8, 'hum' => 59, 'lux' => 115, 'vent' => MonitoringLingkungan::VENTILASI_CUKUP],
                ],
                $batchGrowCampur->id => [
                    ['jam' => 7, 'suhu' => 27.2, 'hum' => 57, 'lux' => 118, 'vent' => MonitoringLingkungan::VENTILASI_BAIK],
                ],
            ];

            foreach ($monitorPlans as $batchId => $entries) {
                foreach ($entries as $entry) {
                    MonitoringLingkungan::updateOrCreate(
                        [
                            'batch_produksi_id' => $batchId,
                            'waktu_pencatatan' => $now->copy()->setTime($entry['jam'], 0)->toDateTimeString(),
                        ],
                        [
                            'kandang_id' => $kandangPembesaran->id,
                            'suhu' => $entry['suhu'],
                            'kelembaban' => $entry['hum'],
                            'intensitas_cahaya' => $entry['lux'],
                            'kondisi_ventilasi' => $entry['vent'],
                            'catatan' => 'Monitoring dummy pembesaran.',
                            'pengguna_id' => 1,
                        ]
                    );
                }
            }

            // Kesehatan & vaksinasi (satu per batch)
            $healthPlans = [
                ['batch' => $batchLayer->id, 'hari' => 2, 'tipe' => Kesehatan::TIPE_VAKSINASI, 'nama' => 'ND-IB Spray'],
            ];

            foreach ($healthPlans as $plan) {
                Kesehatan::updateOrCreate(
                    [
                        'batch_produksi_id' => $plan['batch'],
                        'tanggal' => $now->copy()->subDays($plan['hari'])->toDateString(),
                        'tipe_kegiatan' => $plan['tipe'],
                    ],
                    [
                        'nama_vaksin_obat' => $plan['nama'],
                        'jumlah_burung' => 50,
                        'biaya' => 75000,
                        'petugas' => 'Operator Demo',
                        'catatan' => 'Catatan kesehatan dummy pembesaran.',
                        'pengguna_id' => 1,
                    ]
                );
            }

            // Extend pencatatan harian sejak tanggal masuk sampai hari ini (pakan, kematian kecil, monitoring)
            $rangePlans = [
                [
                    'batch_id' => $batchLayer->id,
                    'start' => $now->copy()->subDays(1),
                    'base_feed' => 6.0,
                    'base_sisa' => 2.0,
                    'harga' => 8500,
                    'base_suhu' => 26.5,
                    'base_hum' => 58,
                    'base_lux' => 120,
                ],
            ];

            foreach ($rangePlans as $plan) {
                $days = $plan['start']->diffInDays($now) + 1;
                for ($i = 0; $i < $days; $i++) {
                    $tanggal = $plan['start']->copy()->addDays($i);

                    // Pakan harian
                    Pakan::updateOrCreate(
                        [
                            'batch_produksi_id' => $plan['batch_id'],
                            'tanggal' => $tanggal->toDateString(),
                        ],
                        [
                            'jumlah_kg' => round($plan['base_feed'] + min(5, $i) * 0.2, 2),
                            'sisa_pakan_kg' => max(0, round($plan['base_sisa'] - $i * 0.15, 2)),
                            'harga_per_kg' => $plan['harga'],
                            'total_biaya' => round(($plan['base_feed'] + min(5, $i) * 0.2) * $plan['harga'], 2),
                            'pengguna_id' => 1,
                        ]
                    );

                    // Kematian kecil tiap 7 hari sekali
                    if ($i > 0 && $i % 7 === 0) {
                        Kematian::updateOrCreate(
                            [
                                'batch_produksi_id' => $plan['batch_id'],
                                'tanggal' => $tanggal->toDateString(),
                            ],
                            [
                                'jumlah' => 1,
                                'penyebab' => Kematian::PENYEBAB_PENYAKIT,
                                'keterangan' => 'Kematian dummy berkala.',
                                'pengguna_id' => 1,
                            ]
                        );
                    }

                    // Monitoring lingkungan tiap 2 hari
                    if ($i % 2 === 0) {
                        MonitoringLingkungan::updateOrCreate(
                            [
                                'batch_produksi_id' => $plan['batch_id'],
                                'waktu_pencatatan' => $tanggal->copy()->setTime(7, 0)->toDateTimeString(),
                            ],
                            [
                                'kandang_id' => $kandangPembesaran->id,
                                'suhu' => round($plan['base_suhu'] + ($i % 3) * 0.2, 2),
                                'kelembaban' => $plan['base_hum'] + ($i % 2),
                                'intensitas_cahaya' => $plan['base_lux'] + ($i % 3) * 3,
                                'kondisi_ventilasi' => MonitoringLingkungan::VENTILASI_BAIK,
                                'catatan' => 'Monitoring otomatis dummy.',
                                'pengguna_id' => 1,
                            ]
                        );
                    }
                }
            }
        });
    }
}
