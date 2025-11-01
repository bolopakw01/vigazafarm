<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('produksi', 'status')) {
            return;
        }

        DB::statement("ALTER TABLE `produksi` MODIFY `status` ENUM('aktif','selesai','dibatalkan','tidak_aktif') NOT NULL DEFAULT 'aktif'");

        DB::table('produksi')
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->update(['status' => 'tidak_aktif']);

        DB::statement("ALTER TABLE `produksi` MODIFY `status` ENUM('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif'");
    }

    public function down(): void
    {
        if (!Schema::hasColumn('produksi', 'status')) {
            return;
        }

        DB::statement("ALTER TABLE `produksi` MODIFY `status` ENUM('aktif','selesai','dibatalkan','tidak_aktif') NOT NULL DEFAULT 'aktif'");

        DB::table('produksi')
            ->where('status', 'tidak_aktif')
            ->update(['status' => 'selesai']);

        DB::statement("ALTER TABLE `produksi` MODIFY `status` ENUM('aktif','selesai','dibatalkan') NOT NULL DEFAULT 'aktif'");
    }
};
