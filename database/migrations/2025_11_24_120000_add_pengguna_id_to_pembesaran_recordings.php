<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'pakan' => 'total_biaya',
            'kematian' => 'keterangan',
            'monitoring_lingkungan' => 'catatan',
            'kesehatan' => 'petugas',
            'berat_sampling' => 'catatan',
        ];

        foreach ($tables as $table => $afterColumn) {
            if (!Schema::hasTable($table) || Schema::hasColumn($table, 'pengguna_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) use ($afterColumn) {
                $table->foreignId('pengguna_id')
                    ->nullable()
                    ->after($afterColumn)
                    ->constrained('pengguna')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['pakan', 'kematian', 'monitoring_lingkungan', 'kesehatan', 'berat_sampling'];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'pengguna_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->dropConstrainedForeignId('pengguna_id');
            });
        }
    }
};
