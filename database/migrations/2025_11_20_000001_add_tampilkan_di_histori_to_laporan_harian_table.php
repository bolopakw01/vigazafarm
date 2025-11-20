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
            if (!Schema::hasColumn('laporan_harian', 'tampilkan_di_histori')) {
                $table->boolean('tampilkan_di_histori')->default(true)->after('catatan_kejadian');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_harian', 'tampilkan_di_histori')) {
                $table->dropColumn('tampilkan_di_histori');
            }
        });
    }
};
