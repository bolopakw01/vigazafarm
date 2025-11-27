<?php

namespace App\Services;

use App\Models\Kandang;
use App\Models\Kematian;
use App\Models\Kesehatan;
use App\Models\MonitoringLingkungan;
use App\Models\Pakan;
use App\Models\Pembesaran;
use App\Models\Penetasan;
use App\Models\PencatatanProduksi;
use App\Models\Produksi;
use App\Models\StokPakan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LookerMasterExportBuilder
{
    protected string $iotSettingsStorage = 'iot_settings.json';

    /**
     * Build all datasets that will be exported to Looker Studio.
     */
    public function datasets(): array
    {
        $datasets = [
            'meta_summary' => $this->buildMetaSummary(),
            'goals_snapshot' => $this->buildGoalsSnapshot(),
            'financial_snapshot' => $this->buildFinancialSnapshot(),
            'iot_settings' => $this->buildIotSettingsDataset(),
            'penetasan' => $this->mapPenetasan(),
            'pembesaran' => $this->mapPembesaran(),
            'produksi' => $this->mapProduksi(),
            'pencatatan_produksi' => $this->mapPencatatanProduksi(),
            'pakan' => $this->mapPakan(),
            'kesehatan' => $this->mapKesehatan(),
            'kematian' => $this->mapKematian(),
            'monitoring_lingkungan' => $this->mapMonitoringLingkungan(),
            'kandang' => $this->mapKandang(),
            'users' => $this->mapUsers(),
        ];

        return collect($datasets)
            ->map(fn (array $rows) => array_values($rows))
            ->toArray();
    }

    /**
     * Build curated CSV definitions for professional bundle (D1, D2, D3).
     */
    public function professionalCsvFiles(): array
    {
        return [
            'laporan_operasional_harian' => $this->buildDailyOperationalReport(),
            'master_status_populasi' => $this->buildPopulationSnapshot(),
            'stok_inventaris' => $this->buildInventorySnapshot(),
        ];
    }

    protected function buildMetaSummary(): array
    {
        $generatedAt = now();

        return [
            [
                'metric' => 'generated_at',
                'value' => $generatedAt->toDateTimeString(),
                'unit' => 'timestamp',
                'notes' => 'Tanggal dan waktu export dibuat',
            ],
            [
                'metric' => 'penetasan_total',
                'value' => Penetasan::count(),
                'unit' => 'records',
                'notes' => 'Total batch penetasan',
            ],
            [
                'metric' => 'pembesaran_total',
                'value' => Pembesaran::count(),
                'unit' => 'records',
                'notes' => 'Total batch pembesaran',
            ],
            [
                'metric' => 'produksi_total',
                'value' => Produksi::count(),
                'unit' => 'records',
                'notes' => 'Total entri produksi',
            ],
            [
                'metric' => 'users_total',
                'value' => User::count(),
                'unit' => 'accounts',
                'notes' => 'Total pengguna aktif',
            ],
        ];
    }

    protected function buildGoalsSnapshot(): array
    {
        $goalsFile = Storage::disk('local')->exists('dashboard_goals.json')
            ? json_decode(Storage::disk('local')->get('dashboard_goals.json'), true)
            : [];

        $goals = collect($goalsFile)->map(function (array $goal) {
            $key = $goal['key'] ?? $goal['title'] ?? null;

            return [
                'key' => $key,
                'title' => $goal['title'] ?? 'Goal',
                'target' => (int) ($goal['target'] ?? 0),
                'current' => (int) ($goal['current'] ?? 0),
                'unit' => $goal['unit'] ?? null,
                'color' => $goal['color'] ?? null,
            ];
        })->values()->all();

        if (empty($goals)) {
            return [];
        }

        return $goals;
    }

    protected function buildFinancialSnapshot(): array
    {
        $pendapatan = (float) PencatatanProduksi::select(DB::raw('COALESCE(SUM(jumlah_produksi * COALESCE(harga_per_unit, 0)), 0) as total'))
            ->value('total');

        $pakanPembesaran = (float) Pakan::whereNotNull('batch_produksi_id')->sum('total_biaya');
        $pakanProduksi = (float) Pakan::whereNotNull('produksi_id')->sum('total_biaya');
        $biayaKesehatan = (float) Kesehatan::sum('biaya');

        $pengeluaran = $pakanPembesaran + $pakanProduksi + $biayaKesehatan;
        $laba = $pendapatan - $pengeluaran;

        return [
            [
                'category' => 'pendapatan',
                'label' => 'Total Pendapatan',
                'amount' => $pendapatan,
                'notes' => 'Akumulasi jumlah_produksi * harga_per_unit',
            ],
            [
                'category' => 'pengeluaran_feed_pembesaran',
                'label' => 'Pengeluaran Pakan Pembesaran',
                'amount' => $pakanPembesaran,
                'notes' => 'Total biaya pakan batch pembesaran',
            ],
            [
                'category' => 'pengeluaran_feed_produksi',
                'label' => 'Pengeluaran Pakan Produksi',
                'amount' => $pakanProduksi,
                'notes' => 'Total biaya pakan produksi',
            ],
            [
                'category' => 'pengeluaran_kesehatan',
                'label' => 'Biaya Kesehatan',
                'amount' => $biayaKesehatan,
                'notes' => 'Total biaya vaksinasi/pengobatan',
            ],
            [
                'category' => 'laba',
                'label' => 'Laba Kotor',
                'amount' => $laba,
                'notes' => 'Pendapatan - Total pengeluaran',
            ],
        ];
    }

    protected function buildIotSettingsDataset(): array
    {
        $default = [
            'mode' => 'simple',
            'api_endpoint' => null,
            'api_key' => null,
            'device_id' => null,
            'update_interval' => 60,
        ];

        if (!Storage::disk('local')->exists($this->iotSettingsStorage)) {
            return [$default];
        }

        $stored = json_decode(Storage::disk('local')->get($this->iotSettingsStorage), true);

        if (!is_array($stored)) {
            return [$default];
        }

        return [[
            'mode' => $stored['mode'] ?? $default['mode'],
            'api_endpoint' => $stored['api_endpoint'] ?? $default['api_endpoint'],
            'api_key' => $stored['api_key'] ?? $default['api_key'],
            'device_id' => $stored['device_id'] ?? $default['device_id'],
            'update_interval' => (int) ($stored['update_interval'] ?? $default['update_interval']),
        ]];
    }

    protected function mapPenetasan(): array
    {
        return Penetasan::with('kandang:id,nama_kandang')
            ->orderByDesc('id')
            ->get()
            ->map(function (Penetasan $penetasan) {
                return [
                    'penetasan_id' => $penetasan->id,
                    'batch' => $penetasan->batch,
                    'kandang' => optional($penetasan->kandang)->nama_kandang,
                    'status' => $penetasan->status,
                    'tanggal_simpan_telur' => $this->formatDate($penetasan->tanggal_simpan_telur),
                    'estimasi_tanggal_menetas' => $this->formatDate($penetasan->estimasi_tanggal_menetas),
                    'tanggal_menetas' => $this->formatDate($penetasan->tanggal_menetas),
                    'jumlah_telur' => (int) ($penetasan->jumlah_telur ?? 0),
                    'jumlah_doc' => (int) ($penetasan->jumlah_doc ?? 0),
                    'jumlah_menetas' => (int) ($penetasan->jumlah_menetas ?? 0),
                    'telur_tidak_fertil' => (int) ($penetasan->telur_tidak_fertil ?? 0),
                    'telur_infertil_ditransfer' => (int) ($penetasan->telur_infertil_ditransfer ?? 0),
                    'doc_ditransfer' => (int) ($penetasan->doc_ditransfer ?? 0),
                    'persentase_tetas' => (float) ($penetasan->persentase_tetas ?? 0),
                    'suhu_penetasan' => (float) ($penetasan->suhu_penetasan ?? 0),
                    'kelembaban_penetasan' => (float) ($penetasan->kelembaban_penetasan ?? 0),
                    'updated_at' => $this->formatDateTime($penetasan->diperbarui_pada),
                ];
            })
            ->toArray();
    }

    protected function mapPembesaran(): array
    {
        return Pembesaran::with(['kandang:id,nama_kandang', 'penetasan:id,batch'])
            ->orderByDesc('id')
            ->get()
            ->map(function (Pembesaran $pembesaran) {
                return [
                    'pembesaran_id' => $pembesaran->id,
                    'kandang' => optional($pembesaran->kandang)->nama_kandang,
                    'sumber_penetasan' => optional($pembesaran->penetasan)->batch,
                    'tanggal_masuk' => $this->formatDate($pembesaran->tanggal_masuk),
                    'tanggal_selesai' => $this->formatDate($pembesaran->tanggal_selesai),
                    'tanggal_siap' => $this->formatDate($pembesaran->tanggal_siap),
                    'jumlah_anak_ayam' => (int) ($pembesaran->jumlah_anak_ayam ?? 0),
                    'jumlah_siap' => (int) ($pembesaran->jumlah_siap ?? 0),
                    'indukan_ditransfer' => (int) ($pembesaran->indukan_ditransfer ?? 0),
                    'status_batch' => $pembesaran->status_batch,
                    'umur_hari' => (int) ($pembesaran->umur_hari ?? 0),
                    'berat_rata_rata' => (float) ($pembesaran->berat_rata_rata ?? 0),
                    'target_berat_akhir' => (float) ($pembesaran->target_berat_akhir ?? 0),
                    'catatan' => $pembesaran->catatan,
                    'updated_at' => $this->formatDateTime($pembesaran->diperbarui_pada),
                ];
            })
            ->toArray();
    }

    protected function mapProduksi(): array
    {
        return Produksi::with(['kandang:id,nama_kandang', 'penetasan:id,batch', 'pembesaran:id'])
            ->orderByDesc('id')
            ->get()
            ->map(function (Produksi $produksi) {
                return [
                    'produksi_id' => $produksi->id,
                    'kandang' => optional($produksi->kandang)->nama_kandang,
                    'tipe_produksi' => $produksi->tipe_produksi,
                    'jenis_input' => $produksi->jenis_input,
                    'tanggal_mulai' => $this->formatDate($produksi->tanggal_mulai),
                    'tanggal_akhir' => $this->formatDate($produksi->tanggal_akhir),
                    'jumlah_telur' => (int) ($produksi->jumlah_telur ?? 0),
                    'jumlah_indukan' => (int) ($produksi->jumlah_indukan ?? 0),
                    'jumlah_jantan' => (int) ($produksi->jumlah_jantan ?? 0),
                    'jumlah_betina' => (int) ($produksi->jumlah_betina ?? 0),
                    'umur_mulai_produksi' => (int) ($produksi->umur_mulai_produksi ?? 0),
                    'berat_rata_rata' => (float) ($produksi->berat_rata_rata ?? 0),
                    'berat_rata_telur' => (float) ($produksi->berat_rata_telur ?? 0),
                    'persentase_fertil' => (float) ($produksi->persentase_fertil ?? 0),
                    'harga_per_pcs' => (float) ($produksi->harga_per_pcs ?? 0),
                    'harga_per_kg' => (float) ($produksi->harga_per_kg ?? 0),
                    'status' => $produksi->status,
                    'catatan' => $produksi->catatan,
                    'sumber_penetasan' => optional($produksi->penetasan)->batch,
                    'sumber_pembesaran_id' => optional($produksi->pembesaran)->id,
                    'updated_at' => $this->formatDateTime($produksi->diperbarui_pada),
                ];
            })
            ->toArray();
    }

    protected function mapPencatatanProduksi(): array
    {
        return PencatatanProduksi::with(['produksi:id,kandang_id', 'produksi.kandang:id,nama_kandang'])
            ->orderByDesc('id')
            ->get()
            ->map(function (PencatatanProduksi $catat) {
                $totalPendapatan = ($catat->jumlah_produksi ?? 0) * ($catat->harga_per_unit ?? 0);

                return [
                    'catatan_id' => $catat->id,
                    'produksi_id' => $catat->produksi_id,
                    'kandang' => optional(optional($catat->produksi)->kandang)->nama_kandang,
                    'tanggal' => $this->formatDate($catat->tanggal),
                    'jumlah_produksi' => (int) ($catat->jumlah_produksi ?? 0),
                    'kualitas' => $catat->kualitas,
                    'berat_rata_rata' => (float) ($catat->berat_rata_rata ?? 0),
                    'harga_per_unit' => (float) ($catat->harga_per_unit ?? 0),
                    'total_pendapatan' => (float) $totalPendapatan,
                    'catatan' => $catat->catatan,
                    'updated_at' => $this->formatDateTime($catat->diperbarui_pada),
                ];
            })
            ->toArray();
    }

    protected function mapPakan(): array
    {
        return Pakan::with(['produksi:id,kandang_id', 'produksi.kandang:id,nama_kandang', 'feedItem:id,name,category'])
            ->orderByDesc('id')
            ->get()
            ->map(function (Pakan $pakan) {
                return [
                    'pakan_id' => $pakan->id,
                    'produksi_id' => $pakan->produksi_id,
                    'batch_produksi_id' => $pakan->batch_produksi_id,
                    'tanggal' => $this->formatDate($pakan->tanggal),
                    'jumlah_kg' => (float) ($pakan->jumlah_kg ?? 0),
                    'jumlah_karung' => (int) ($pakan->jumlah_karung ?? 0),
                    'harga_per_kg' => (float) ($pakan->harga_per_kg ?? 0),
                    'total_biaya' => (float) ($pakan->total_biaya ?? 0),
                    'feed_item' => optional($pakan->feedItem)->name,
                    'feed_category' => optional($pakan->feedItem)->category,
                    'kandang' => optional(optional($pakan->produksi)->kandang)->nama_kandang,
                    'updated_at' => $this->formatDateTime($pakan->diperbarui_pada),
                ];
            })
            ->toArray();
    }

    protected function mapKesehatan(): array
    {
        return Kesehatan::orderByDesc('id')
            ->get()
            ->map(function (Kesehatan $kesehatan) {
                return [
                    'kesehatan_id' => $kesehatan->id,
                    'batch_produksi_id' => $kesehatan->batch_produksi_id,
                    'tanggal' => $this->formatDate($kesehatan->tanggal),
                    'tipe_kegiatan' => $kesehatan->tipe_kegiatan,
                    'nama_vaksin_obat' => $kesehatan->nama_vaksin_obat,
                    'jumlah_burung' => (int) ($kesehatan->jumlah_burung ?? 0),
                    'biaya' => (float) ($kesehatan->biaya ?? 0),
                    'petugas' => $kesehatan->petugas,
                    'catatan' => $kesehatan->catatan,
                    'updated_at' => $this->formatDateTime($kesehatan->diperbarui_pada),
                ];
            })
            ->toArray();
    }

    protected function mapKematian(): array
    {
        return Kematian::orderByDesc('id')
            ->get()
            ->map(function (Kematian $kematian) {
                return [
                    'kematian_id' => $kematian->id,
                    'batch_produksi_id' => $kematian->batch_produksi_id,
                    'produksi_id' => $kematian->produksi_id,
                    'tanggal' => $this->formatDate($kematian->tanggal),
                    'jumlah' => (int) ($kematian->jumlah ?? 0),
                    'penyebab' => $kematian->penyebab,
                    'keterangan' => $kematian->keterangan,
                    'updated_at' => $this->formatDateTime($kematian->diperbarui_pada),
                ];
            })
            ->toArray();
    }

    protected function mapMonitoringLingkungan(): array
    {
        return MonitoringLingkungan::with('kandang:id,nama_kandang')
            ->orderByDesc('id')
            ->get()
            ->map(function (MonitoringLingkungan $monitor) {
                return [
                    'monitoring_id' => $monitor->id,
                    'kandang' => optional($monitor->kandang)->nama_kandang,
                    'batch_produksi_id' => $monitor->batch_produksi_id,
                    'waktu_pencatatan' => $this->formatDateTime($monitor->waktu_pencatatan),
                    'suhu' => (float) ($monitor->suhu ?? 0),
                    'kelembaban' => (float) ($monitor->kelembaban ?? 0),
                    'intensitas_cahaya' => (float) ($monitor->intensitas_cahaya ?? 0),
                    'kondisi_ventilasi' => $monitor->kondisi_ventilasi,
                    'catatan' => $monitor->catatan,
                    'updated_at' => $this->formatDateTime($monitor->diperbarui_pada),
                ];
            })
            ->toArray();
    }

    protected function mapKandang(): array
    {
        return Kandang::withTrashed()
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Kandang $kandang) {
                return [
                    'kandang_id' => $kandang->id,
                    'kode_kandang' => $kandang->kode_kandang,
                    'nama_kandang' => $kandang->nama_kandang,
                    'tipe_kandang' => $kandang->tipe_kandang,
                    'status' => $kandang->status,
                    'kapasitas_maksimal' => (int) ($kandang->kapasitas_maksimal ?? 0),
                    'kapasitas_terpakai' => (int) ($kandang->kapasitas_terpakai ?? 0),
                    'deleted_at' => $this->formatDateTime($kandang->deleted_at),
                    'created_at' => $this->formatDateTime($kandang->created_at),
                    'updated_at' => $this->formatDateTime($kandang->updated_at),
                ];
            })
            ->toArray();
    }

    protected function mapUsers(): array
    {
        return User::orderByDesc('dibuat_pada')
            ->get()
            ->map(function (User $user) {
                return [
                    'user_id' => $user->id,
                    'nama' => $user->nama,
                    'username' => $user->nama_pengguna,
                    'email' => $user->surel,
                    'peran' => $user->peran,
                    'alamat' => $user->alamat,
                    'created_at' => $this->formatDateTime($user->dibuat_pada ?? $user->created_at),
                    'updated_at' => $this->formatDateTime($user->diperbarui_pada ?? $user->updated_at),
                ];
            })
            ->toArray();
    }

    protected function buildDailyOperationalReport(): array
    {
        $columns = [
            'tanggal',
            'kode_batch',
            'nama_kandang',
            'fase_produksi',
            'populasi_aktif',
            'kematian_ekor',
            'produksi_telur_butir',
            'telur_rusak_butir',
            'pakan_terpakai_kg',
            'biaya_pakan_harian',
            'pendapatan_harian',
            'profit_harian',
            'fcr_harian',
            'hdp_persen',
        ];

        $feedLookup = $this->buildDailyFeedLookup();
        $deathLookup = $this->buildDailyDeathLookup();

        $records = PencatatanProduksi::with([
                'produksi.kandang',
                'produksi.penetasan',
                'produksi.pembesaran',
            ])
            ->orderBy('tanggal')
            ->get();

        $rows = $records->map(function (PencatatanProduksi $record) use ($feedLookup, $deathLookup) {
            $produksi = $record->produksi;

            if (!$produksi) {
                return null;
            }

            $date = $this->formatDate($record->tanggal);
            $key = $this->makeDailyKey($produksi->id, $produksi->batch_produksi_id, $date);
            $feed = $feedLookup[$key] ?? ['kg' => 0.0, 'cost' => 0.0];
            $kematian = $deathLookup[$key] ?? 0;

            $populasiAktif = (int) ($produksi->jumlah_indukan ?? $produksi->jumlah_betina ?? 0);
            $produksiTelur = (int) ($record->jumlah_produksi ?? 0);
            $telurRusak = $this->estimateEggDefectCount($record);

            $pendapatan = $produksiTelur * (float) ($record->harga_per_unit ?? 0);
            $biayaPakan = (float) ($feed['cost'] ?? 0);
            $profit = $pendapatan - $biayaPakan;

            $fase = $this->determineFaseProduksi($produksi);

            $fcr = ($produksiTelur > 0 && ($feed['kg'] ?? 0) > 0)
                ? round($feed['kg'] / max($produksiTelur, 1), 4)
                : null;

            $hdp = ($fase === 'Layer' && $populasiAktif > 0 && $produksiTelur > 0)
                ? round(($produksiTelur / $populasiAktif) * 100, 2)
                : null;

            return [
                'tanggal' => $date,
                'kode_batch' => $this->resolveBatchCodeFromProduksi($produksi),
                'nama_kandang' => optional($produksi->kandang)->nama_kandang,
                'fase_produksi' => $fase,
                'populasi_aktif' => $populasiAktif,
                'kematian_ekor' => $kematian,
                'produksi_telur_butir' => $produksiTelur,
                'telur_rusak_butir' => $telurRusak,
                'pakan_terpakai_kg' => round((float) ($feed['kg'] ?? 0), 2),
                'biaya_pakan_harian' => round($biayaPakan, 2),
                'pendapatan_harian' => round($pendapatan, 2),
                'profit_harian' => round($profit, 2),
                'fcr_harian' => $fcr,
                'hdp_persen' => $hdp,
            ];
        })
        ->filter()
        ->values()
        ->toArray();

        return [
            'columns' => $columns,
            'rows' => $rows,
        ];
    }

    protected function buildPopulationSnapshot(): array
    {
        $columns = [
            'kode_batch',
            'jenis_hewan',
            'lokasi_kandang',
            'tanggal_masuk',
            'target_panen',
            'umur_hari',
            'populasi_awal',
            'populasi_saat_ini',
            'persentase_hidup',
            'status_batch',
        ];

        $rows = [];
        $deathByBatch = $this->fetchTotalDeathByBatch();
        $deathByProduksi = $this->fetchTotalDeathByProduksi();

        Pembesaran::with(['kandang', 'penetasan'])
            ->orderByDesc('id')
            ->get()
            ->each(function (Pembesaran $batch) use (&$rows, $deathByBatch) {
                $kodeBatch = $batch->penetasan->batch ?? sprintf('PB-%04d', $batch->id);
                $popAwal = (int) ($batch->jumlah_anak_ayam ?? 0);
                $popSekarang = max($popAwal - ($deathByBatch[$batch->id] ?? 0), 0);

                $rows[$kodeBatch] = [
                    'kode_batch' => $kodeBatch,
                    'jenis_hewan' => 'Puyuh',
                    'lokasi_kandang' => optional($batch->kandang)->nama_kandang,
                    'tanggal_masuk' => $this->formatDate($batch->tanggal_masuk),
                    'target_panen' => $this->formatDate($batch->tanggal_siap ?? $batch->tanggal_selesai),
                    'umur_hari' => (int) ($batch->umur_hari ?? 0),
                    'populasi_awal' => $popAwal,
                    'populasi_saat_ini' => $popSekarang,
                    'persentase_hidup' => $this->calculatePercentage($popSekarang, $popAwal),
                    'status_batch' => $this->formatStatusLabel($batch->status_batch ?? 'Aktif'),
                ];
            });

        Produksi::with(['kandang', 'penetasan'])
            ->orderByDesc('id')
            ->get()
            ->each(function (Produksi $produksi) use (&$rows, $deathByProduksi) {
                $kodeBatch = $this->resolveBatchCodeFromProduksi($produksi);
                $popAwal = (int) ($produksi->jumlah_indukan ?? $produksi->jumlah_betina ?? 0);
                $popSekarang = max($popAwal - ($deathByProduksi[$produksi->id] ?? 0), 0);

                $rows[$kodeBatch] = [
                    'kode_batch' => $kodeBatch,
                    'jenis_hewan' => $this->resolveJenisHewan($produksi->tipe_produksi),
                    'lokasi_kandang' => optional($produksi->kandang)->nama_kandang,
                    'tanggal_masuk' => $this->formatDate($produksi->tanggal_mulai),
                    'target_panen' => $this->formatDate($produksi->tanggal_akhir),
                    'umur_hari' => $this->calculateUmurHari($produksi->tanggal_mulai),
                    'populasi_awal' => $popAwal,
                    'populasi_saat_ini' => $popSekarang,
                    'persentase_hidup' => $this->calculatePercentage($popSekarang, $popAwal),
                    'status_batch' => $this->formatStatusLabel($produksi->status ?? 'Aktif'),
                ];
            });

        return [
            'columns' => $columns,
            'rows' => array_values($rows),
        ];
    }

    protected function buildInventorySnapshot(): array
    {
        $columns = [
            'kode_barang',
            'nama_item',
            'kategori',
            'supplier',
            'stok_fisik_satuan',
            'stok_berat_total',
            'harga_rata_rata',
            'total_nilai_aset',
            'status_stok',
            'tanggal_kadaluarsa',
        ];

        $rows = StokPakan::orderBy('nama_pakan')
            ->get()
            ->map(function (StokPakan $stok) {
                $stokKg = (float) ($stok->stok_kg ?? 0);
                $harga = (float) ($stok->harga_per_kg ?? 0);

                return [
                    'kode_barang' => $stok->kode_pakan ?? sprintf('STK-%04d', $stok->id),
                    'nama_item' => $stok->nama_pakan,
                    'kategori' => $this->resolveInventoryCategory($stok->jenis_pakan),
                    'supplier' => $stok->supplier,
                    'stok_fisik_satuan' => (float) ($stok->stok_karung ?? 0),
                    'stok_berat_total' => round($stokKg, 2),
                    'harga_rata_rata' => round($harga, 2),
                    'total_nilai_aset' => round($stokKg * $harga, 2),
                    'status_stok' => $this->resolveInventoryStatus($stok),
                    'tanggal_kadaluarsa' => $this->formatDate($stok->tanggal_kadaluarsa),
                ];
            })
            ->toArray();

        return [
            'columns' => $columns,
            'rows' => $rows,
        ];
    }

    protected function buildDailyFeedLookup(): array
    {
        return Pakan::selectRaw('COALESCE(produksi_id, 0) as produksi_id, COALESCE(batch_produksi_id, 0) as batch_id, DATE(tanggal) as tanggal, SUM(jumlah_kg) as total_kg, SUM(total_biaya) as total_biaya')
            ->groupBy('produksi_id', 'batch_produksi_id', 'tanggal')
            ->get()
            ->mapWithKeys(function ($row) {
                $produksiId = $row->produksi_id ? (int) $row->produksi_id : null;
                $batchId = $row->batch_id ? (int) $row->batch_id : null;
                $key = $this->makeDailyKey($produksiId, $batchId, $row->tanggal);

                return [
                    $key => [
                        'kg' => (float) $row->total_kg,
                        'cost' => (float) $row->total_biaya,
                    ],
                ];
            })
            ->toArray();
    }

    protected function buildDailyDeathLookup(): array
    {
        return Kematian::selectRaw('COALESCE(produksi_id, 0) as produksi_id, COALESCE(batch_produksi_id, 0) as batch_id, DATE(tanggal) as tanggal, SUM(jumlah) as total')
            ->groupBy('produksi_id', 'batch_produksi_id', 'tanggal')
            ->get()
            ->mapWithKeys(function ($row) {
                $produksiId = $row->produksi_id ? (int) $row->produksi_id : null;
                $batchId = $row->batch_id ? (int) $row->batch_id : null;
                $key = $this->makeDailyKey($produksiId, $batchId, $row->tanggal);

                return [
                    $key => (int) $row->total,
                ];
            })
            ->toArray();
    }

    protected function fetchTotalDeathByBatch(): array
    {
        return Kematian::selectRaw('batch_produksi_id, SUM(jumlah) as total')
            ->whereNotNull('batch_produksi_id')
            ->groupBy('batch_produksi_id')
            ->pluck('total', 'batch_produksi_id')
            ->map(fn ($value) => (int) $value)
            ->toArray();
    }

    protected function fetchTotalDeathByProduksi(): array
    {
        return Kematian::selectRaw('produksi_id, SUM(jumlah) as total')
            ->whereNotNull('produksi_id')
            ->groupBy('produksi_id')
            ->pluck('total', 'produksi_id')
            ->map(fn ($value) => (int) $value)
            ->toArray();
    }

    protected function makeDailyKey(?int $produksiId, ?int $batchId, ?string $date): string
    {
        $idPart = $produksiId ? 'p' . $produksiId : 'p0';
        $batchPart = $batchId ? 'b' . $batchId : 'b0';
        $datePart = $date ?: '0000-00-00';

        return implode('|', [$idPart, $batchPart, $datePart]);
    }

    protected function estimateEggDefectCount(PencatatanProduksi $record): int
    {
        $quality = strtolower((string) ($record->kualitas ?? ''));

        if ($quality === '') {
            return 0;
        }

        $keywords = ['rusak', 'reject', 'pecah'];

        foreach ($keywords as $keyword) {
            if (str_contains($quality, $keyword)) {
                return (int) ($record->jumlah_produksi ?? 0);
            }
        }

        return 0;
    }

    protected function determineFaseProduksi(?Produksi $produksi): string
    {
        if (!$produksi) {
            return 'Layer';
        }

        $raw = strtolower((string) ($produksi->tipe_produksi ?? ''));

        if (str_contains($raw, 'penetas')) {
            return 'Penetasan';
        }

        if (str_contains($raw, 'grow') || str_contains($raw, 'besar')) {
            return 'Pembesaran';
        }

        if (str_contains($raw, 'layer') || str_contains($raw, 'telur')) {
            return 'Layer';
        }

        if (!empty($produksi->pembesaran_id)) {
            return 'Pembesaran';
        }

        return 'Layer';
    }

    protected function resolveBatchCodeFromProduksi(?Produksi $produksi): string
    {
        if (!$produksi) {
            return 'BATCH-UNKNOWN';
        }

        if (!empty($produksi->batch_produksi_id)) {
            return sprintf('BATCH-%04d', $produksi->batch_produksi_id);
        }

        if ($produksi->penetasan && $produksi->penetasan->batch) {
            return $produksi->penetasan->batch;
        }

        if (!empty($produksi->pembesaran_id)) {
            return sprintf('PB-%04d', $produksi->pembesaran_id);
        }

        return sprintf('PROD-%04d', $produksi->id);
    }

    protected function resolveJenisHewan(?string $raw): string
    {
        $value = strtolower((string) ($raw ?? ''));

        if (str_contains($value, 'ayam')) {
            return 'Ayam';
        }

        if (str_contains($value, 'bebek') || str_contains($value, 'itik')) {
            return 'Bebek';
        }

        return 'Puyuh';
    }

    protected function calculateUmurHari($tanggalMulai): ?int
    {
        if (!$tanggalMulai) {
            return null;
        }

        return Carbon::parse($tanggalMulai)->diffInDays(now());
    }

    protected function calculatePercentage($current, $initial): ?float
    {
        if (!$initial || $initial == 0) {
            return null;
        }

        return round(($current / $initial) * 100, 2);
    }

    protected function formatStatusLabel(?string $raw): string
    {
        $value = trim((string) ($raw ?? ''));

        return $value !== '' ? ucwords(strtolower($value)) : 'Aktif';
    }

    protected function resolveInventoryCategory(?string $raw): string
    {
        $value = strtolower((string) ($raw ?? ''));

        if (str_contains($value, 'vitamin')) {
            return 'Vitamin';
        }

        if (str_contains($value, 'vaksin') || str_contains($value, 'obat')) {
            return 'Vaksin';
        }

        return 'Pakan';
    }

    protected function resolveInventoryStatus(StokPakan $stok): string
    {
        if ($stok->isExpired() || ($stok->stok_kg ?? 0) <= 0) {
            return 'Kritis';
        }

        if ($stok->isNearExpiry() || $stok->isLowStock()) {
            return 'Warning';
        }

        return 'Aman';
    }

    protected function formatDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_string($value) && trim($value) !== '') {
            return date('Y-m-d', strtotime($value));
        }

        return null;
    }

    protected function formatDateTime($value): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_string($value) && trim($value) !== '') {
            return date('Y-m-d H:i:s', strtotime($value));
        }

        return null;
    }
}
