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
        Schema::table('vf_laporan_harian', function (Blueprint $table) {
            $table->dropForeign('vf_laporan_harian_batch_produksi_id_foreign');
            $table->dropUnique('vf_laporan_harian_batch_produksi_id_tanggal_unique');
            $table->foreign('batch_produksi_id', 'vf_laporan_harian_batch_produksi_id_foreign')->references('id')->on('vf_batch_produksi')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vf_laporan_harian', function (Blueprint $table) {
            $table->dropForeign('vf_laporan_harian_batch_produksi_id_foreign');
            $table->unique(['batch_produksi_id', 'tanggal'], 'vf_laporan_harian_batch_produksi_id_tanggal_unique');
            $table->foreign('batch_produksi_id', 'vf_laporan_harian_batch_produksi_id_foreign')->references('id')->on('vf_batch_produksi')->cascadeOnDelete();
        });
    }
};
