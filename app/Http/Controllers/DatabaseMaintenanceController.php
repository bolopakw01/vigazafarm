<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseMaintenanceController extends Controller
{
    protected string $disk = 'local';
    protected string $directory = 'db-backups';

    public function showBackup()
    {
        return view('admin.pages.sistem.database.dbbackup', [
            'backups' => $this->getBackupFiles(),
        ]);
    }

    public function runBackup()
    {
        $payload = $this->exportDatabase();
        $filename = 'vigaza-backup-' . now()->format('Ymd_His') . '.json';

        Storage::disk($this->disk)->put(
            $this->pathFor($filename),
            json_encode($payload, JSON_PRETTY_PRINT)
        );

        return redirect()->route('admin.sistem.database.backup')
            ->with('success', 'Backup database berhasil dibuat.');
    }

    public function downloadBackup(string $filename): BinaryFileResponse
    {
        $sanitized = $this->sanitizeFilename($filename);
        $path = $this->pathFor($sanitized);

        $disk = Storage::disk($this->disk);

        if (!$disk->exists($path)) {
            abort(404);
        }

        return response()->download($disk->path($path), $sanitized);
    }

    public function deleteBackup(string $filename)
    {
        $sanitized = $this->sanitizeFilename($filename);
        $path = $this->pathFor($sanitized);

        if (!Storage::disk($this->disk)->exists($path)) {
            return redirect()->route('admin.sistem.database.backup')
                ->with('error', 'File backup tidak ditemukan.');
        }

        Storage::disk($this->disk)->delete($path);

        return redirect()->route('admin.sistem.database.backup')
            ->with('success', 'File backup berhasil dihapus.');
    }

    public function showRestore()
    {
        return view('admin.pages.sistem.database.dbrestore', [
            'backups' => $this->getBackupFiles(),
        ]);
    }

    public function runRestore(Request $request)
    {
        $validated = $request->validate([
            'source' => 'required|in:existing,upload',
            'filename' => 'required_if:source,existing|string',
            'backup_file' => 'required_if:source,upload|file|mimetypes:application/json,text/plain,application/octet-stream|max:20480',
        ]);

        try {
            $payload = $validated['source'] === 'existing'
                ? $this->readBackupFromStorage($validated['filename'])
                : $this->readBackupFromUpload($request);

            $this->restoreFromPayload($payload);
        } catch (\Throwable $throwable) {
            report($throwable);

            return back()
                ->withErrors(['restore' => 'Gagal melakukan restore database: ' . $throwable->getMessage()])
                ->withInput();
        }

        return redirect()->route('admin.sistem.database.restore')
            ->with('success', 'Database berhasil direstore dari backup.');
    }

    public function showConnection()
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        return view('admin.pages.sistem.database.dbkoneksi', [
            'connection' => [
                'driver' => $connection,
                'host' => Arr::get($config, 'host'),
                'port' => Arr::get($config, 'port'),
                'database' => Arr::get($config, 'database'),
                'username' => Arr::get($config, 'username'),
                'password' => Arr::get($config, 'password'),
            ],
        ]);
    }

    public function updateConnection(Request $request)
    {
        $validated = $request->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|numeric|min:1',
            'database' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        $this->writeEnvValues([
            'DB_HOST' => $validated['host'],
            'DB_PORT' => (string) $validated['port'],
            'DB_DATABASE' => $validated['database'],
            'DB_USERNAME' => $validated['username'],
            'DB_PASSWORD' => $validated['password'] ?? '',
        ]);

        Artisan::call('config:clear');

        return redirect()->route('admin.sistem.database.connection')
            ->with('success', 'Koneksi database berhasil diperbarui.');
    }

    public function showOptimization()
    {
        return view('admin.pages.sistem.database.dboptimasi', [
            'tables' => $this->getTableStatistics(),
            'last_optimization' => Cache::get('db_last_optimization'),
        ]);
    }

    public function runOptimization()
    {
        $tables = $this->listTables();

        foreach ($tables as $table) {
            try {
                DB::statement('OPTIMIZE TABLE `' . $table . '`');
                DB::statement('ANALYZE TABLE `' . $table . '`');
            } catch (\Throwable $throwable) {
                report($throwable);
            }
        }

        Cache::put('db_last_optimization', now()->toDateTimeString());

        return redirect()->route('admin.sistem.database.optimization')
            ->with('success', 'Optimasi database selesai dijalankan.');
    }

    protected function exportDatabase(): array
    {
        $connection = DB::connection();
        $database = $connection->getDatabaseName();
        $tables = $this->listTables();

        $export = [
            'generated_at' => now()->toIso8601String(),
            'connection' => $database,
            'table_count' => count($tables),
            'tables' => [],
        ];

        foreach ($tables as $table) {
            $rows = $connection->table($table)->get()->map(function ($row) {
                return (array) $row;
            })->toArray();

            $export['tables'][$table] = $rows;
        }

        return $export;
    }

    protected function restoreFromPayload(array $payload): void
    {
        if (!isset($payload['tables']) || !is_array($payload['tables'])) {
            throw new \RuntimeException('Format file backup tidak dikenali.');
        }

        $tables = $payload['tables'];

        DB::beginTransaction();
        Schema::disableForeignKeyConstraints();

        try {
            foreach ($tables as $table => $rows) {
                if (!Schema::hasTable($table)) {
                    continue;
                }

                DB::table($table)->truncate();

                if (!empty($rows)) {
                    foreach (array_chunk($rows, 500) as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    protected function readBackupFromStorage(string $filename): array
    {
        $sanitized = $this->sanitizeFilename($filename);
        $path = $this->pathFor($sanitized);

        if (!Storage::disk($this->disk)->exists($path)) {
            throw new \RuntimeException('File backup tidak ditemukan.');
        }

        $content = Storage::disk($this->disk)->get($path);

        return $this->decodeBackupContent($content);
    }

    protected function readBackupFromUpload(Request $request): array
    {
        $file = $request->file('backup_file');

        if (!$file) {
            throw new \RuntimeException('File backup tidak dapat diakses.');
        }

        return $this->decodeBackupContent($file->get());
    }

    protected function decodeBackupContent(string $content): array
    {
        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('File backup bukan JSON yang valid.');
        }

        return $decoded;
    }

    protected function getBackupFiles(): array
    {
        $disk = Storage::disk($this->disk);

        if (!$disk->exists($this->directory)) {
            return [];
        }

        return collect($disk->files($this->directory))
            ->filter(fn ($path) => Str::endsWith(Str::lower($path), '.json'))
            ->map(function ($path) use ($disk) {
                return [
                    'name' => basename($path),
                    'size' => $disk->size($path),
                    'created_at' => $disk->lastModified($path),
                ];
            })
            ->sortByDesc('created_at')
            ->values()
            ->all();
    }

    protected function listTables(): array
    {
        $connection = DB::connection();
        $database = $connection->getDatabaseName();
        $key = 'Tables_in_' . $database;

        return collect($connection->select('SHOW TABLES'))
            ->map(fn ($row) => $row->$key ?? null)
            ->filter()
            ->values()
            ->all();
    }

    protected function pathFor(string $filename): string
    {
        return $this->directory . '/' . $filename;
    }

    protected function sanitizeFilename(string $filename): string
    {
        return basename($filename);
    }

    protected function writeEnvValues(array $pairs): void
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            throw new \RuntimeException('.env file tidak ditemukan.');
        }

        $content = file_get_contents($path);

        foreach ($pairs as $key => $value) {
            $escaped = $this->escapeEnvValue($value);
            $pattern = "/^{$key}=.*$/m";
            $replacement = $key . '=' . $escaped;

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacement, $content);
            } else {
                $content .= PHP_EOL . $replacement;
            }
        }

        file_put_contents($path, $content);
    }

    protected function escapeEnvValue(?string $value): string
    {
        $value = $value ?? '';

        if ($value === '') {
            return '';
        }

        if (Str::contains($value, [' ', '#', '='])) {
            return '"' . str_replace('"', '\\"', $value) . '"';
        }

        return $value;
    }

    protected function getTableStatistics(): array
    {
        $database = DB::connection()->getDatabaseName();

        $rows = DB::select('
            SELECT table_name, engine, table_rows, data_length, index_length, create_time, update_time
            FROM information_schema.TABLES
            WHERE table_schema = ?
            ORDER BY (data_length + index_length) DESC
        ', [$database]);

        return collect($rows)
            ->map(function ($row) {
                return [
                    'name' => $row->table_name,
                    'engine' => $row->engine,
                    'rows' => (int) $row->table_rows,
                    'size' => (float) ($row->data_length + $row->index_length),
                    'data_length' => (float) $row->data_length,
                    'index_length' => (float) $row->index_length,
                    'created_at' => $row->create_time,
                    'updated_at' => $row->update_time,
                ];
            })
            ->all();
    }
}
