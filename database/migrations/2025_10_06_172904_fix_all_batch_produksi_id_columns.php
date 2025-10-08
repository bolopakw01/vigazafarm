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
        // Fix batch_produksi_id type di semua tabel yang masih bigint
        $tables = ['kematian', 'monitoring_lingkungan', 'kesehatan', 'laporan_harian'];
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table_blueprint) {
                // Drop foreign key jika ada
                try {
                    $table_blueprint->dropForeign(['batch_produksi_id']);
                } catch (\Exception $e) {
                    // Ignore jika foreign key tidak ada
                }
                
                // Ubah tipe kolom dari bigint ke varchar
                $table_blueprint->string('batch_produksi_id', 50)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke bigint jika rollback
        $tables = ['kematian', 'monitoring_lingkungan', 'kesehatan', 'laporan_harian'];
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table_blueprint) {
                try {
                    $table_blueprint->dropForeign(['batch_produksi_id']);
                } catch (\Exception $e) {
                    // Ignore
                }
                
                $table_blueprint->unsignedBigInteger('batch_produksi_id')->nullable()->change();
            });
        }
    }
};
