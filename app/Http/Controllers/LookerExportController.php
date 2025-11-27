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
        $timestamp = now()->format('Ymd_His');
        $zipFilename = "vigaza-master-dashboard-{$timestamp}.zip";
        $tempPath = storage_path('app/' . $zipFilename);

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file export.');
        }

        foreach ($datasets as $name => $rows) {
            $csv = $this->convertToCsv($name, $rows);
            $zip->addFromString($name . '.csv', $csv);
        }

        $zip->close();

        return response()->download($tempPath, $zipFilename)->deleteFileAfterSend(true);
    }

    public function downloadSingleCsv(): BinaryFileResponse
    {
        $builder = new LookerMasterExportBuilder();
        $datasets = $builder->professionalCsvFiles();
        $timestamp = now()->format('Ymd_His');
        $zipFilename = "vigaza-professional-dashboard-{$timestamp}.zip";
        $tempPath = storage_path('app/' . $zipFilename);

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

        $zip->close();

        return response()->download($tempPath, $zipFilename)->deleteFileAfterSend(true);
    }

    protected function convertToCsv(string $dataset, array $rows): string
    {
        $handle = fopen('php://temp', 'r+');

        if (empty($rows)) {
            fputcsv($handle, ['dataset', 'message']);
            fputcsv($handle, [$dataset, 'Tidak ada data tersedia']);
        } else {
            $headers = array_keys(reset($rows));
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                $ordered = [];
                foreach ($headers as $header) {
                    $value = $row[$header] ?? '';
                    $ordered[] = is_bool($value) ? ($value ? '1' : '0') : $value;
                }
                fputcsv($handle, $ordered);
            }
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv ?: '';
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
