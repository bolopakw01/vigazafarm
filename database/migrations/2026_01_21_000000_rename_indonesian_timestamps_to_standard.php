<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'vf_pengguna',
            'vf_penetasan',
            'vf_pembesaran',
            'vf_produksi',
            'vf_pencatatan_produksi',
            'vf_pakan',
            'vf_kematian',
            'vf_monitoring_lingkungan',
            'vf_laporan_harian',
            'vf_berat_sampling',
            'vf_kesehatan',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} CHANGE COLUMN dibuat_pada created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
            DB::statement("ALTER TABLE {$table} CHANGE COLUMN diperbarui_pada updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        }
    }

    public function down(): void
    {
        $tables = [
            'vf_pengguna',
            'vf_penetasan',
            'vf_pembesaran',
            'vf_produksi',
            'vf_pencatatan_produksi',
            'vf_pakan',
            'vf_kematian',
            'vf_monitoring_lingkungan',
            'vf_laporan_harian',
            'vf_berat_sampling',
            'vf_kesehatan',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} CHANGE COLUMN created_at dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
            DB::statement("ALTER TABLE {$table} CHANGE COLUMN updated_at diperbarui_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        }
    }
};

