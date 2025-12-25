<?php

namespace App\Http\Controllers;

use App\Services\LookerMasterExportBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class LookerExportController extends Controller
{
    public function index()
    {
        $builder = new LookerMasterExportBuilder();
        $datasets = $builder->datasets();

        $stats = collect($datasets)->map(function (array $rows, string $key) {
            return [
                'key' => $key,
                'rows' => count($rows),
            ];
        })->values();

        return view('admin.pages.sistem.dashboard.looker-export', [
            'datasetStats' => $stats,
            'generatedAt' => now(),
        ]);
    }

    public function download(): BinaryFileResponse
    {
        $builder = new LookerMasterExportBuilder();
        $datasets = $builder->datasets();
        $schema = $builder->schema();
        $timestamp = now()->format('Ymd_His');
        $zipFilename = "vigaza-master-dashboard-{$timestamp}.zip";
        $tempPath = storage_path('app/' . $zipFilename);

        $this->ensureZipExtension();

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file export.');
        }

        foreach ($datasets as $name => $rows) {
            $csv = $this->convertToCsv($name, $rows);
            $zip->addFromString($name . '.csv', $csv);
        }

        $zip->addFromString('schema_manifest.json', json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $zip->addFromString('README.txt', $this->readmeText());

        $zip->close();

        return response()->download($tempPath, $zipFilename)->deleteFileAfterSend(true);
    }

    public function downloadSingleCsv(): BinaryFileResponse
    {
        $builder = new LookerMasterExportBuilder();
        $datasets = $builder->professionalCsvFiles();
        $schema = $builder->schema();
        $timestamp = now()->format('Ymd_His');
        $zipFilename = "vigaza-professional-dashboard-{$timestamp}.zip";
        $tempPath = storage_path('app/' . $zipFilename);

        $this->ensureZipExtension();

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat paket CSV profesional.');
        }

        foreach ($datasets as $name => $dataset) {
            $columns = $dataset['columns'] ?? [];
            $rows = $dataset['rows'] ?? [];
            $csv = $this->convertRowsToCsv($columns, $rows);
            $zip->addFromString($name . '.csv', $csv);
        }

        $zip->addFromString('schema_manifest.json', json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $zip->addFromString('README.txt', $this->readmeText());

        $zip->close();

        return response()->download($tempPath, $zipFilename)->deleteFileAfterSend(true);
    }

    public function downloadFlatSingle()
    {
        $builder = new LookerMasterExportBuilder();
        $datasets = $builder->professionalCsvFiles();
        $main = $datasets['laporan_operasional_harian'] ?? null;

        if (!$main) {
            abort(404, 'Dataset utama tidak tersedia.');
        }

        $columns = $main['columns'] ?? [];
        $rows = $main['rows'] ?? [];
        $csv = $this->convertRowsToCsv($columns, $rows);
        $timestamp = now()->format('Ymd_His');
        $filename = "vigaza-laporan-operasional-{$timestamp}.csv";

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function ensureZipExtension(): void
    {
        if (!class_exists(ZipArchive::class)) {
            abort(500, 'Ekstensi ZIP PHP tidak aktif. Silakan aktifkan php_zip di PHP.ini atau pasang zip extension.');
        }
    }

    protected function convertToCsv(string $dataset, array $rows): string
    {
        $handle = fopen('php://temp', 'r+');
        $headers = empty($rows)
            ? $this->expectedHeaders($dataset)
            : array_keys(reset($rows));

        if (!empty($headers)) {
            fputcsv($handle, $headers);
        }

        foreach ($rows as $row) {
            $ordered = [];
            foreach ($headers as $header) {
                $value = $row[$header] ?? '';
                $ordered[] = is_bool($value) ? ($value ? '1' : '0') : $value;
            }
            fputcsv($handle, $ordered);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv ?: '';
    }

    protected function expectedHeaders(string $dataset): array
    {
        return match ($dataset) {
            'meta_summary' => ['metric', 'value', 'unit', 'notes'],
            'goals_snapshot' => ['key', 'title', 'target', 'current', 'unit', 'color'],
            'financial_snapshot' => ['category', 'label', 'amount', 'notes'],
            'iot_settings' => ['mode', 'api_endpoint', 'api_key', 'device_id', 'update_interval'],
            'penetasan' => ['penetasan_id', 'batch', 'kandang', 'status', 'tanggal_simpan_telur', 'estimasi_tanggal_menetas', 'tanggal_menetas', 'jumlah_telur', 'jumlah_doc', 'jumlah_menetas', 'telur_tidak_fertil', 'telur_infertil_ditransfer', 'doc_ditransfer', 'persentase_tetas', 'suhu_penetasan', 'kelembaban_penetasan', 'updated_at'],
            'pembesaran' => ['pembesaran_id', 'kandang', 'sumber_penetasan', 'tanggal_masuk', 'tanggal_selesai', 'tanggal_siap', 'jumlah_anak_ayam', 'jumlah_siap', 'indukan_ditransfer', 'status_batch', 'umur_hari', 'berat_rata_rata', 'target_berat_akhir', 'catatan', 'updated_at'],
            'produksi' => ['produksi_id', 'kandang', 'tipe_produksi', 'jenis_input', 'tanggal_mulai', 'tanggal_akhir', 'jumlah_telur', 'jumlah_indukan', 'jumlah_jantan', 'jumlah_betina', 'umur_mulai_produksi', 'berat_rata_rata', 'berat_rata_telur', 'persentase_fertil', 'harga_per_pcs', 'harga_per_kg', 'status', 'catatan', 'sumber_penetasan', 'sumber_pembesaran_id', 'updated_at'],
            'pencatatan_produksi' => ['catatan_id', 'produksi_id', 'kandang', 'tanggal', 'jumlah_produksi', 'kualitas', 'berat_rata_rata', 'harga_per_unit', 'total_pendapatan', 'catatan', 'updated_at'],
            'pakan' => ['pakan_id', 'produksi_id', 'batch_produksi_id', 'tanggal', 'jumlah_kg', 'sisa_pakan_kg', 'jumlah_karung', 'harga_per_kg', 'total_biaya', 'feed_item', 'feed_category', 'kandang', 'updated_at'],
            'kesehatan' => ['kesehatan_id', 'batch_produksi_id', 'tanggal', 'tipe_kegiatan', 'nama_vaksin_obat', 'jumlah_burung', 'biaya', 'petugas', 'catatan', 'updated_at'],
            'kematian' => ['kematian_id', 'batch_produksi_id', 'produksi_id', 'tanggal', 'jumlah', 'penyebab', 'keterangan', 'updated_at'],
            'monitoring_lingkungan' => ['monitoring_id', 'kandang', 'batch_produksi_id', 'waktu_pencatatan', 'suhu', 'kelembaban', 'intensitas_cahaya', 'kondisi_ventilasi', 'catatan', 'updated_at'],
            'kandang' => ['kandang_id', 'kode_kandang', 'nama_kandang', 'tipe_kandang', 'status', 'kapasitas_maksimal', 'kapasitas_terpakai', 'deleted_at', 'created_at', 'updated_at'],
            'users' => ['user_id', 'nama', 'username', 'email', 'peran', 'alamat', 'created_at', 'updated_at'],
            default => [],
        };
    }

    protected function readmeText(): string
    {
        return implode("\n", [
            'Vigaza Farm – Looker Studio Export',
            '==================================',
            '',
            'Berisi CSV dengan header stabil untuk dihubungkan ke Looker Studio.',
            '',
            'Cara cepat:',
            '1) Unggah ZIP ini dan ekstrak lokal.',
            '2) Di Looker Studio, pilih sumber data > File Upload > pilih CSV sesuai kebutuhan.',
            '3) Gunakan schema_manifest.json untuk mapping Dimension/Metric & tipe data.',
            '4) Format tanggal: DATE = Y-m-d, DATETIME = Y-m-d H:i:s.',
            '',
            'Bundle master: semua dataset kasar.',
            'Bundle profesional: CSV terkurasi (laporan_operasional_harian, master_status_populasi, stok_inventaris).',
            '',
            'Jika kolom kosong, header tetap ada agar mapping tidak berubah.',
        ]);
    }

    protected function convertRowsToCsv(array $columns, array $rows): string
    {
        $headers = $columns;

        if (empty($headers) && !empty($rows)) {
            $headers = array_keys(reset($rows));
        }

        $handle = fopen('php://temp', 'r+');

        if (!empty($headers)) {
            fputcsv($handle, $headers);
        }

        foreach ($rows as $row) {
            $ordered = [];

            foreach ($headers as $header) {
                $ordered[] = $this->formatCsvValue($row[$header] ?? '');
            }

            fputcsv($handle, $ordered);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv ?: '';
    }

    protected function formatCsvValue($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return $value ?? '';
    }
}
