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
        Schema::table('laporan_harian', function (Blueprint $table) {
            try {
                $table->dropUnique('unique_batch_tanggal');
            } catch (Exception $e) {
                // Index might not exist, continue
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->unique(['batch_produksi_id', 'tanggal'], 'unique_batch_tanggal');
        });
    }
};
