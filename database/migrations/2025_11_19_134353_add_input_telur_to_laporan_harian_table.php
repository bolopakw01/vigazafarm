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
            if (!Schema::hasColumn('laporan_harian', 'input_telur')) {
                $table->integer('input_telur')->nullable()->after('produksi_telur')->comment('Jumlah input telur per aksi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_harian', 'input_telur')) {
                $table->dropColumn('input_telur');
            }
        });
    }
};
