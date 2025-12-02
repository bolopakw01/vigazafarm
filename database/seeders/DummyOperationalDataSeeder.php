<?php

namespace Database\Seeders;

use App\Models\BatchProduksi;
use App\Models\BeratSampling;
use App\Models\Kandang;
use App\Models\Kematian;
use App\Models\Kesehatan;
use App\Models\LaporanHarian;
use App\Models\MonitoringLingkungan;
use App\Models\Pakan;
use App\Models\PencatatanProduksi;
use App\Models\Penetasan;
use App\Models\Pembesaran;
use App\Models\Produksi;
use App\Models\StokPakan;
use App\Models\FeedVitaminItem;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DummyOperationalDataSeeder extends Seeder
{
    /**
     * Seed dummy operational data for October-November 2025.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $owner = User::where('peran', 'owner')->first();
            $operator = User::where('peran', 'operator')->first();
            $defaultUserId = $operator?->id ?? $owner?->id;

            if (!$defaultUserId) {
                $this->command?->warn('Skipping DummyOperationalDataSeeder because no owner/operator user exists.');
                return;
            }

            $kandangs = $this->seedKandangs();
            $stokPakan = $this->seedStokPakan();
            $feedItems = $this->seedFeedVitaminItems();

            $batchConfigs = [
                [
                    'code' => 'BCH-2410-PUY-01',
                    'fase' => 'layer',
                    'kandang_code' => 'KDG-PUYUH-01',
                    'start' => Carbon::create(2025, 10, 1),
                    'end' => Carbon::create(2025, 11, 30),
                    'initial_population' => 1400,
                    'jumlah_jantan' => 260,
                    'jumlah_betina' => 1140,
                    'feed_intake_kg' => 0.034,
                    'feed_price' => 8600,
                    'egg_price' => 310,
                    'vitamin_price' => 52000,
                    'stok_key' => 'QP-BREED-40',
                    'feed_item_key' => 'Breeder Balance',
                    'avg_egg_weight' => 11.2,
                    'tipe_produksi' => 'puyuh',
                    'jenis_input' => 'dari_pembesaran',
                    'egg_sale_ratio' => 0.7,
                    'cull_price' => 4200,
                    'cull_frequency' => 2,
                    'cull_size' => 48,
                    'harga_per_pcs' => 4800,
                    'grower_extra_doc' => 90,
                    'hatch' => [
                        'code' => 'PNTS-2409-BR01',
                        'kandang_code' => 'KDG-TETAS-01',
                        'storage_date' => '2025-09-10',
                        'hatcher_date' => '2025-09-25',
                        'hatch_date' => '2025-09-29',
                        'telur' => 1800,
                        'menetas' => 1600,
                        'doc' => 1505,
                        'suhu' => 37.5,
                        'kelembaban' => 63,
                    ],
                    'grower_kandang_code' => 'KDG-GROW-01',
                    'health_events' => [
                        [
                            'tanggal' => '2025-10-04',
                            'tipe' => Kesehatan::TIPE_VAKSINASI,
                            'nama' => 'ND IB kombinasi',
                            'jumlah' => 1400,
                            'biaya' => 520000,
                            'catatan' => 'Vaksin dasar flok breeder',
                        ],
                        [
                            'tanggal' => '2025-10-18',
                            'tipe' => Kesehatan::TIPE_PEMERIKSAAN,
                            'nama' => 'Screening bobot & FCR',
                            'jumlah' => 1385,
                            'biaya' => 145000,
                            'catatan' => 'Evaluasi performa minggu ke-3',
                        ],
                        [
                            'tanggal' => '2025-11-06',
                            'tipe' => Kesehatan::TIPE_PENGOBATAN,
                            'nama' => 'Elektrolit + multivit',
                            'jumlah' => 120,
                            'biaya' => 112000,
                            'catatan' => 'Pemulihan stres pasca-sortasi',
                        ],
                        [
                            'tanggal' => '2025-11-22',
                            'tipe' => Kesehatan::TIPE_VAKSINASI,
                            'nama' => 'ND killed booster',
                            'jumlah' => 1350,
                            'biaya' => 545000,
                            'catatan' => 'Penguatan jelang siklus produksi puncak',
                        ],
                    ],
                ],
                [
                    'code' => 'BCH-2410-LYR-01',
                    'fase' => 'layer',
                    'kandang_code' => 'KDG-LAYER-02',
                    'start' => Carbon::create(2025, 10, 12),
                    'end' => Carbon::create(2025, 11, 30),
                    'initial_population' => 1200,
                    'jumlah_jantan' => 80,
                    'jumlah_betina' => 1120,
                    'feed_intake_kg' => 0.032,
                    'feed_price' => 8200,
                    'egg_price' => 325,
                    'vitamin_price' => 48000,
                    'stok_key' => 'QP-LAYER-50',
                    'feed_item_key' => 'Layer Complete',
                    'avg_egg_weight' => 11.6,
                    'tipe_produksi' => 'telur',
                    'jenis_input' => 'manual',
                    'egg_sale_ratio' => 0.78,
                    'cull_price' => 3500,
                    'cull_frequency' => 3,
                    'cull_size' => 28,
                    'harga_per_pcs' => 325,
                    'grower_extra_doc' => 60,
                    'hatch' => [
                        'code' => 'PNTS-2410-SET1',
                        'kandang_code' => 'KDG-TETAS-01',
                        'storage_date' => '2025-09-15',
                        'hatcher_date' => '2025-09-29',
                        'hatch_date' => '2025-10-01',
                        'telur' => 1500,
                        'menetas' => 1350,
                        'doc' => 1280,
                        'suhu' => 37.6,
                        'kelembaban' => 62,
                    ],
                    'grower_kandang_code' => 'KDG-GROW-01',
                    'health_events' => [
                        [
                            'tanggal' => '2025-10-15',
                            'tipe' => Kesehatan::TIPE_VAKSINASI,
                            'nama' => 'ND Live (primer)',
                            'jumlah' => 1200,
                            'biaya' => 450000,
                            'catatan' => 'Booster awal masa layer',
                        ],
                        [
                            'tanggal' => '2025-10-29',
                            'tipe' => Kesehatan::TIPE_PEMERIKSAAN,
                            'nama' => 'Pemeriksaan rutin',
                            'jumlah' => 1200,
                            'biaya' => 125000,
                            'catatan' => 'Evaluasi bobot dan konsumsi',
                        ],
                        [
                            'tanggal' => '2025-11-12',
                            'tipe' => Kesehatan::TIPE_PENGOBATAN,
                            'nama' => 'Antibiotik ringan',
                            'jumlah' => 60,
                            'biaya' => 98000,
                            'catatan' => 'Batuk ringan pada sebagian flok',
                        ],
                        [
                            'tanggal' => '2025-11-26',
                            'tipe' => Kesehatan::TIPE_VAKSINASI,
                            'nama' => 'ND + IB (booster)',
                            'jumlah' => 1180,
                            'biaya' => 470000,
                            'catatan' => 'Booster jelang puncak produksi',
                        ],
                    ],
                ],
            ];

            foreach ($batchConfigs as $batchConfig) {
                $this->seedBatch(
                    $batchConfig,
                    $kandangs,
                    $stokPakan,
                    $feedItems,
                    $owner,
                    $operator,
                    $defaultUserId
                );
            }
        });
    }

    /**
     * Ensure kandang master data is present.
     */
    private function seedKandangs(): array
    {
        $definitions = [
            'KDG-TETAS-01' => [
                'nama' => 'Setter & Hatcher 01',
                'tipe' => 'penetasan',
                'kapasitas' => 2000,
                'keterangan' => 'Ruang setter dan hatcher terkontrol',
            ],
            'KDG-GROW-01' => [
                'nama' => 'Grower Tunnel 01',
                'tipe' => 'pembesaran',
                'kapasitas' => 1500,
                'keterangan' => 'Tunnel ventilation dengan pemanas otomatis',
            ],
            'KDG-LAYER-01' => [
                'nama' => 'Layer House Alpha',
                'tipe' => 'produksi',
                'kapasitas' => 1500,
                'keterangan' => 'Kandang baterai 3 tingkat',
            ],
            'KDG-PUYUH-01' => [
                'nama' => 'Breeder House Beta',
                'tipe' => 'produksi',
                'kapasitas' => 1800,
                'keterangan' => 'Kandang breeder fokus indukan puyuh',
            ],
            'KDG-LAYER-02' => [
                'nama' => 'Layer House Beta',
                'tipe' => 'produksi',
                'kapasitas' => 1600,
                'keterangan' => 'Cluster layer dengan sistem nipple drinker',
            ],
        ];

        $result = [];

        foreach ($definitions as $code => $payload) {
            $result[$code] = Kandang::updateOrCreate(
                ['kode_kandang' => $code],
                [
                    'nama_kandang' => $payload['nama'],
                    'kapasitas_maksimal' => $payload['kapasitas'],
                    'tipe_kandang' => $payload['tipe'],
                    'status' => 'aktif',
                    'keterangan' => $payload['keterangan'],
                ]
            );
        }

        return $result;
    }

    /**
     * Ensure feed stock master data exists.
     */
    private function seedStokPakan(): array
    {
        $definitions = [
            'QP-LAYER-50' => [
                'nama' => 'Quail Prime Layer 50kg',
                'jenis' => 'Layer',
                'merek' => 'Quail Prime',
                'harga' => 8200,
                'stokKg' => 1800,
                'stokKarung' => 36,
                'beratKarung' => 50,
                'supplier' => 'CV Pakan Maju',
            ],
            'QP-BREED-40' => [
                'nama' => 'Quail Breeder Nutrisi 40kg',
                'jenis' => 'Breeder',
                'merek' => 'NutriQ',
                'harga' => 8600,
                'stokKg' => 1400,
                'stokKarung' => 35,
                'beratKarung' => 40,
                'supplier' => 'PT Pakan Sejahtera',
            ],
        ];

        $result = [];

        foreach ($definitions as $code => $payload) {
            $result[$code] = StokPakan::updateOrCreate(
                ['kode_pakan' => $code],
                [
                    'nama_pakan' => $payload['nama'],
                    'jenis_pakan' => $payload['jenis'],
                    'merek' => $payload['merek'],
                    'harga_per_kg' => $payload['harga'],
                    'stok_kg' => $payload['stokKg'],
                    'stok_karung' => $payload['stokKarung'],
                    'berat_per_karung' => $payload['beratKarung'],
                    'supplier' => $payload['supplier'],
                ]
            );
        }

        return $result;
    }

    /**
     * Ensure feed & vitamin items exist.
     */
    private function seedFeedVitaminItems(): array
    {
        $definitions = [
            'Layer Complete' => [
                'category' => 'pakan',
                'price' => 410000,
                'unit' => 'karung',
            ],
            'Multi Vitamin Plus' => [
                'category' => 'vitamin',
                'price' => 48000,
                'unit' => 'liter',
            ],
            'Breeder Balance' => [
                'category' => 'pakan',
                'price' => 445000,
                'unit' => 'karung',
            ],
        ];

        $result = [];

        foreach ($definitions as $name => $payload) {
            $result[$name] = FeedVitaminItem::updateOrCreate(
                ['name' => $name, 'category' => $payload['category']],
                [
                    'price' => $payload['price'],
                    'unit' => $payload['unit'],
                    'is_active' => true,
                ]
            );
        }

        return $result;
    }

    /**
     * Seed a single batch worth of operational data.
     */
    private function seedBatch(
        array $batch,
        array $kandangs,
        array $stokPakan,
        array $feedItems,
        ?User $owner,
        ?User $operator,
        int $defaultUserId
    ): Produksi {
        $batchCode = $batch['code'];
        $kandang = $kandangs[$batch['kandang_code']];
        $stok = $stokPakan[$batch['stok_key']];
        $startingStockKg = $stok->stok_kg ?? 0;
        $feedItem = $feedItems[$batch['feed_item_key']];
        $now = now();
        $tipeProduksi = $batch['tipe_produksi'] ?? 'telur';
        $jenisInput = $batch['jenis_input'] ?? 'dari_pembesaran';
        $docBuffer = $batch['grower_extra_doc'] ?? 60;
        $jumlahJantan = $batch['jumlah_jantan'] ?? 80;
        $jumlahBetina = $batch['jumlah_betina'] ?? max(0, $batch['initial_population'] - $jumlahJantan);
        $penetasanTable = (new Penetasan())->getTable();
        $pembesaranTable = (new Pembesaran())->getTable();
        $produksiTable = (new Produksi())->getTable();

        $produksiIds = Produksi::where('batch_produksi_id', $batchCode)->pluck('id');
        if ($produksiIds->isNotEmpty()) {
            PencatatanProduksi::whereIn('produksi_id', $produksiIds)->delete();
        }

        LaporanHarian::where('batch_produksi_id', $batchCode)->delete();
        Pakan::where('batch_produksi_id', $batchCode)->delete();
        Kematian::where('batch_produksi_id', $batchCode)->delete();
        MonitoringLingkungan::where('batch_produksi_id', $batchCode)->delete();
        Kesehatan::where('batch_produksi_id', $batchCode)->delete();
        BeratSampling::where('batch_produksi_id', $batchCode)->delete();
        Produksi::whereIn('id', $produksiIds)->delete();
        Pembesaran::where('batch_produksi_id', $batchCode)->delete();
        BatchProduksi::where('kode_batch', $batchCode)->delete();

        $penetasanPayload = [
            'kandang_id' => $kandangs[$batch['hatch']['kandang_code']]->id,
            'tanggal_simpan_telur' => $batch['hatch']['storage_date'],
            'tanggal_masuk_hatcher' => $batch['hatch']['hatcher_date'],
            'estimasi_tanggal_menetas' => Carbon::parse($batch['hatch']['hatch_date'])->subDay(),
            'tanggal_menetas' => $batch['hatch']['hatch_date'],
            'jumlah_telur' => $batch['hatch']['telur'],
            'jumlah_menetas' => $batch['hatch']['menetas'],
            'jumlah_doc' => $batch['hatch']['doc'],
            'suhu_penetasan' => $batch['hatch']['suhu'],
            'kelembaban_penetasan' => $batch['hatch']['kelembaban'],
            'telur_tidak_fertil' => $batch['hatch']['telur'] - $batch['hatch']['menetas'],
            'persentase_tetas' => round(($batch['hatch']['menetas'] / $batch['hatch']['telur']) * 100, 2),
            'doc_ditransfer' => $batch['initial_population'],
            'fase_penetasan' => 'hatcher',
            'status' => 'selesai',
            'catatan' => 'Dummy data Oktober 2025',
        ];
        $penetasanPayload = $this->attachAuditColumns(
            $penetasanTable,
            $penetasanPayload,
            $owner?->id,
            $operator?->id ?? $owner?->id
        );

        $penetasan = Penetasan::updateOrCreate(
            ['batch' => $batch['hatch']['code']],
            $penetasanPayload
        );

        $pembesaranPayload = [
            'kandang_id' => $kandangs[$batch['grower_kandang_code']]->id,
            'penetasan_id' => $penetasan->id,
            'tanggal_masuk' => Carbon::parse($batch['start'])->subDays(5),
            'jumlah_anak_ayam' => $batch['initial_population'] + $docBuffer,
            'jenis_kelamin' => 'mix',
            'status_batch' => 'Aktif',
            'tanggal_siap' => Carbon::parse($batch['start'])->subDay(),
            'jumlah_siap' => $batch['initial_population'],
            'umur_hari' => 45,
            'berat_rata_rata' => 165,
            'target_berat_akhir' => 185,
            'kondisi_doc' => 'Baik',
            'catatan' => 'Batch siap memasuki kandang produksi',
            'indukan_ditransfer' => $batch['initial_population'],
        ];
        $pembesaranPayload = $this->attachAuditColumns(
            $pembesaranTable,
            $pembesaranPayload,
            $owner?->id,
            $operator?->id ?? $owner?->id
        );

        $pembesaran = Pembesaran::updateOrCreate(
            ['batch_produksi_id' => $batchCode],
            $pembesaranPayload
        );

        $batchRecord = BatchProduksi::create([
            'kode_batch' => $batchCode,
            'kandang_id' => $kandang->id,
            'tanggal_mulai' => $batch['start']->toDateString(),
            'jumlah_awal' => $batch['initial_population'],
            'jumlah_saat_ini' => $batch['initial_population'],
            'fase' => $batch['fase'],
            'status' => 'aktif',
            'catatan' => 'Dummy data Oktober-November 2025',
        ]);

        $produksiPayload = [
            'kandang_id' => $kandang->id,
            'batch_produksi_id' => $batchCode,
            'penetasan_id' => $penetasan->id,
            'pembesaran_id' => $pembesaran->id,
            'tipe_produksi' => $tipeProduksi,
            'jenis_input' => $jenisInput,
            'produksi_sumber_id' => $batch['produksi_sumber_id'] ?? null,
            'tanggal_mulai' => $batch['start']->toDateString(),
            'tanggal_akhir' => $batch['end']->toDateString(),
            'jumlah_telur' => $batch['jumlah_telur'] ?? null,
            'tanggal' => $batch['start']->toDateString(),
            'jumlah_indukan' => $batch['initial_population'],
            'jumlah_jantan' => $jumlahJantan,
            'jumlah_betina' => $jumlahBetina,
            'umur_mulai_produksi' => 50,
            'berat_rata_rata' => 182,
            'berat_rata_telur' => $batch['avg_egg_weight'],
            'persentase_fertil' => 86,
            'harga_per_pcs' => $batch['harga_per_pcs'] ?? $batch['egg_price'],
            'harga_per_kg' => $batch['harga_per_kg'] ?? 40500,
            'status' => 'aktif',
            'catatan' => 'Dataset simulasi Oktober-November 2025',
        ];
        $produksiPayload = $this->filterColumns($produksiTable, $produksiPayload);

        $produksi = Produksi::create($produksiPayload);

        $dailyData = $this->buildDailyTimelines(
            $batch,
            $produksi,
            $kandang,
            $stok,
            $feedItem,
            $owner,
            $operator,
            $defaultUserId
        );

        if (!empty($dailyData['laporan'])) {
            LaporanHarian::insert($dailyData['laporan']);
        }
        if (!empty($dailyData['pakan'])) {
            Pakan::insert($dailyData['pakan']);
        }
        if (!empty($dailyData['kematian'])) {
            Kematian::insert($dailyData['kematian']);
        }
        if (!empty($dailyData['monitoring'])) {
            MonitoringLingkungan::insert($dailyData['monitoring']);
        }
        if (!empty($dailyData['berat'])) {
            BeratSampling::insert($dailyData['berat']);
        }
        if (!empty($dailyData['pencatatan'])) {
            PencatatanProduksi::insert($dailyData['pencatatan']);
        }

        $healthRows = [];
        foreach ($batch['health_events'] as $event) {
            $healthRows[] = [
                'batch_produksi_id' => $batchCode,
                'tanggal' => $event['tanggal'],
                'tipe_kegiatan' => $event['tipe'],
                'nama_vaksin_obat' => $event['nama'],
                'jumlah_burung' => $event['jumlah'],
                'catatan' => $event['catatan'],
                'biaya' => $event['biaya'],
                'petugas' => 'Tim Kesehatan',
                'pengguna_id' => $operator?->id ?? $defaultUserId,
                'dibuat_pada' => Carbon::parse($event['tanggal'])->endOfDay(),
                'diperbarui_pada' => Carbon::parse($event['tanggal'])->endOfDay(),
            ];
        }

        if (!empty($healthRows)) {
            Kesehatan::insert($healthRows);
        }

        $batchRecord->update(['jumlah_saat_ini' => $dailyData['final_population']]);
        $newStockKg = max(0, round($startingStockKg - $dailyData['total_feed'], 2));
        $stok->update([
            'stok_kg' => $newStockKg,
            'stok_karung' => max(0, (int) round($newStockKg / max(1, $stok->berat_per_karung ?: 50))),
        ]);

        return $produksi;
    }

    /**
     * Build daily operational records for a batch.
     */
    private function buildDailyTimelines(
        array $batch,
        Produksi $produksi,
        Kandang $kandang,
        StokPakan $stok,
        FeedVitaminItem $feedItem,
        ?User $owner,
        ?User $operator,
        int $defaultUserId
    ): array {
        $start = $batch['start']->copy()->startOfDay();
        $end = $batch['end']->copy()->startOfDay();
        $period = CarbonPeriod::create($start, $end);
        $initialPopulation = $batch['initial_population'];
        $currentBirds = $initialPopulation;
        $totalDeaths = 0;
        $weeklyEggTotal = 0;
        $prevSisaPakan = 240.0;
        $vitaminStock = 18.0;
        $laporan = [];
        $pakan = [];
        $kematian = [];
        $monitoring = [];
        $berat = [];
        $pencatatan = [];
        $totalFeed = 0.0;
        $userId = $operator?->id ?? $defaultUserId;
        $tipeProduksi = $batch['tipe_produksi'] ?? 'telur';
        $isPuyuhBatch = $tipeProduksi === 'puyuh';
        $saleRatio = $batch['egg_sale_ratio'] ?? ($isPuyuhBatch ? 0.72 : 0.78);
        $cullPrice = $batch['cull_price'] ?? 3500;
        $cullFrequency = $batch['cull_frequency'] ?? ($isPuyuhBatch ? 2 : 3);
        $cullSize = $batch['cull_size'] ?? ($isPuyuhBatch ? 45 : 30);

        foreach ($period as $date) {
            $dayIndex = $start->diffInDays($date);
            $dailyDeaths = $this->simulateDeath($dayIndex, $currentBirds);
            $dailyDeaths = min($dailyDeaths, $currentBirds > 10 ? $currentBirds - 5 : 0);
            $currentBirds -= $dailyDeaths;
            $totalDeaths += $dailyDeaths;

            $hdp = $this->simulateHdp($dayIndex);
            if ($isPuyuhBatch) {
                $hdp = max(70, min(88, $hdp - 1.2));
            } else {
                $hdp = min(94, $hdp + 0.6);
            }
            $eggCount = (int) round($currentBirds * ($hdp / 100));
            $feedKg = round($currentBirds * $batch['feed_intake_kg'], 2);
            $deliveryKg = in_array($date->dayOfWeekIso, [1, 4], true) ? 150 : 0;
            $prevSisaPakan = max(0, round($prevSisaPakan + $deliveryKg - $feedKg, 2));
            $totalFeed += $feedKg;

            $vitaminUse = in_array($date->dayOfWeekIso, [2, 5], true) ? 0.45 : 0.0;
            $vitaminStock = max(0, round($vitaminStock - $vitaminUse, 2));

            $soldEggs = (int) round($eggCount * $saleRatio);
            $cullSale = ($date->isSaturday() && $dayIndex % $cullFrequency === 0) ? $cullSize : 0;
            $pendapatanTelur = $soldEggs * $batch['egg_price'];
            $pendapatanCull = $cullSale * $cullPrice;

            $laporan[] = [
                'batch_produksi_id' => $batch['code'],
                'tanggal' => $date->toDateString(),
                'jumlah_burung' => $currentBirds,
                'produksi_telur' => $eggCount,
                'nama_tray' => 'Tray plastik 30 butir',
                'keterangan_tray' => 'Standar layer house',
                'jumlah_kematian' => $dailyDeaths,
                'jenis_kelamin_kematian' => $dailyDeaths ? 'betina' : null,
                'keterangan_kematian' => $dailyDeaths ? 'Seleksi alam/cedera ringan' : null,
                'konsumsi_pakan_kg' => $feedKg,
                'sisa_pakan_kg' => $prevSisaPakan,
                'harga_pakan_per_kg' => $batch['feed_price'],
                'biaya_pakan_harian' => round($feedKg * $batch['feed_price'], 2),
                'sisa_tray_bal' => round(4.0 + (($dayIndex % 4) * 0.35), 2),
                'sisa_tray_lembar' => 60 + (($dayIndex + 2) % 5) * 6,
                'sisa_vitamin_liter' => $vitaminStock,
                'vitamin_terpakai' => $vitaminUse,
                'harga_vitamin_per_liter' => $batch['vitamin_price'],
                'biaya_vitamin_harian' => round($vitaminUse * $batch['vitamin_price'], 2),
                'sisa_telur' => max(0, $eggCount - $soldEggs),
                'penjualan_telur_butir' => $soldEggs,
                'penjualan_puyuh_ekor' => $cullSale,
                'jenis_kelamin_penjualan' => $cullSale ? 'betina' : null,
                'pendapatan_harian' => $pendapatanTelur + $pendapatanCull,
                'tray_penjualan_id' => null,
                'harga_per_butir' => $batch['egg_price'],
                'nama_tray_penjualan' => $soldEggs ? 'Tray fiber reuse' : null,
                'fcr' => $this->calculateFcr($feedKg, $eggCount, $batch['avg_egg_weight']),
                'hen_day_production' => round($hdp, 2),
                'mortalitas_kumulatif' => round(($totalDeaths / $initialPopulation) * 100, 2),
                'catatan_kejadian' => $this->buildDailyNote($dayIndex, $dailyDeaths, $cullSale),
                'tampilkan_di_histori' => true,
                'pengguna_id' => $userId,
                'dibuat_pada' => $date->copy()->endOfDay(),
                'diperbarui_pada' => $date->copy()->endOfDay(),
            ];

            $pakan[] = [
                'produksi_id' => $produksi->id,
                'stok_pakan_id' => $stok->id,
                'feed_item_id' => $feedItem->id,
                'batch_produksi_id' => $batch['code'],
                'tanggal' => $date->toDateString(),
                'jumlah_kg' => $feedKg,
                'sisa_pakan_kg' => $prevSisaPakan,
                'jumlah_karung' => (int) max(1, round($feedKg / max(1, $stok->berat_per_karung ?? 50))),
                'harga_per_kg' => $batch['feed_price'],
                'total_biaya' => round($feedKg * $batch['feed_price'], 2),
                'pengguna_id' => $userId,
                'dibuat_pada' => $date->copy()->endOfDay(),
                'diperbarui_pada' => $date->copy()->endOfDay(),
            ];

            if ($dailyDeaths > 0) {
                $kematian[] = [
                    'produksi_id' => $produksi->id,
                    'batch_produksi_id' => $batch['code'],
                    'tanggal' => $date->toDateString(),
                    'jumlah' => $dailyDeaths,
                    'penyebab' => Arr::random([
                        Kematian::PENYEBAB_PENYAKIT,
                        Kematian::PENYEBAB_STRESS,
                        Kematian::PENYEBAB_KECELAKAAN,
                        Kematian::PENYEBAB_TIDAK_DIKETAHUI,
                    ]),
                    'keterangan' => 'Pencatatan otomatis dummy data',
                    'pengguna_id' => $userId,
                    'dibuat_pada' => $date->copy()->endOfDay(),
                    'diperbarui_pada' => $date->copy()->endOfDay(),
                ];
            }

            $monitoring[] = [
                'kandang_id' => $kandang->id,
                'batch_produksi_id' => $batch['code'],
                'waktu_pencatatan' => $date->copy()->setTime(9, 0, 0),
                'suhu' => round(26 + sin($dayIndex / 7) * 1.2 + $this->randomFloat(-0.4, 0.4), 2),
                'kelembaban' => round(60 + cos($dayIndex / 6) * 2.4 + $this->randomFloat(-1, 1), 2),
                'intensitas_cahaya' => round(320 + ($dayIndex % 5) * 6 + $this->randomFloat(-8, 8), 2),
                'kondisi_ventilasi' => Arr::random([
                    MonitoringLingkungan::VENTILASI_BAIK,
                    MonitoringLingkungan::VENTILASI_CUKUP,
                    MonitoringLingkungan::VENTILASI_BAIK,
                ]),
                'catatan' => 'Monitoring harian otomatis',
                'pengguna_id' => $userId,
                'dibuat_pada' => $date->copy()->endOfDay(),
                'diperbarui_pada' => $date->copy()->endOfDay(),
            ];

            if ($date->isWednesday() && $dayIndex >= 7) {
                $berat[] = [
                    'batch_produksi_id' => $batch['code'],
                    'tanggal_sampling' => $date->toDateString(),
                    'umur_hari' => 50 + $dayIndex,
                    'berat_rata_rata' => round(174 + sin($dayIndex / 5) * 4 + $this->randomFloat(-2.5, 2.5), 2),
                    'jumlah_sampel' => 35,
                    'catatan' => 'Sampling mingguan dummy',
                    'pengguna_id' => $userId,
                    'dibuat_pada' => $date->copy()->endOfDay(),
                    'diperbarui_pada' => $date->copy()->endOfDay(),
                ];
            }

            $weeklyEggTotal += $eggCount;
            if ($date->isSunday()) {
                $pencatatan[] = [
                    'produksi_id' => $produksi->id,
                    'tanggal' => $date->toDateString(),
                    'jumlah_produksi' => $weeklyEggTotal,
                    'kualitas' => 'baik',
                    'berat_rata_rata' => $batch['avg_egg_weight'],
                    'harga_per_unit' => $batch['egg_price'],
                    'catatan' => 'Rekap minggu ke-' . (intdiv($dayIndex, 7) + 1),
                    'dibuat_oleh' => $userId,
                    'created_at' => $date->copy()->endOfDay(),
                    'updated_at' => $date->copy()->endOfDay(),
                ];
                $weeklyEggTotal = 0;
            }
        }

        return [
            'laporan' => $laporan,
            'pakan' => $pakan,
            'kematian' => $kematian,
            'monitoring' => $monitoring,
            'berat' => $berat,
            'pencatatan' => $pencatatan,
            'final_population' => $currentBirds,
            'total_feed' => $totalFeed,
        ];
    }

    private function simulateDeath(int $dayIndex, int $currentBirds): int
    {
        if ($currentBirds < 200) {
            return 0;
        }

        $base = $dayIndex % 11 === 0 ? 2 : 0;
        $random = random_int(0, 1);
        return $base + $random;
    }

    private function simulateHdp(int $dayIndex): float
    {
        $seasonal = sin($dayIndex / 6) * 2.5;
        $noise = $this->randomFloat(-2.5, 2.5);
        return max(74, min(92, 80 + $seasonal + $noise));
    }

    private function calculateFcr(float $feedKg, int $eggCount, float $avgEggWeight): float
    {
        $eggKg = ($eggCount * $avgEggWeight) / 1000;
        if ($eggKg <= 0) {
            return 0;
        }

        return round($feedKg / $eggKg, 2);
    }

    private function buildDailyNote(int $dayIndex, int $dailyDeaths, int $cullSale): string
    {
        if ($dailyDeaths > 0) {
            return 'Kematian ringan tercatat dan sudah dibuang';
        }

        if ($cullSale > 0) {
            return 'Seleksi afkir kecil untuk menjaga performa';
        }

        if ($dayIndex % 7 === 0) {
            return 'Produksi stabil, tidak ada catatan khusus';
        }

        return 'Operasional berjalan normal';
    }

    private function randomFloat(float $min, float $max): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    private function attachAuditColumns(string $table, array $payload, ?int $creatorId, ?int $updaterId): array
    {
        if ($creatorId && Schema::hasColumn($table, 'created_by')) {
            $payload['created_by'] = $creatorId;
        }

        if ($updaterId && Schema::hasColumn($table, 'updated_by')) {
            $payload['updated_by'] = $updaterId;
        }

        return $payload;
    }

    private function filterColumns(string $table, array $payload): array
    {
        $filtered = [];

        foreach ($payload as $column => $value) {
            if (Schema::hasColumn($table, $column)) {
                $filtered[$column] = $value;
            }
        }

        return $filtered;
    }
}
