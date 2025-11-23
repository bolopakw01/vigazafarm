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
        Schema::table('tray_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('tray_histories', 'old_nama_tray')) {
                $table->string('old_nama_tray', 120)->nullable()->after('keterangan');
            }

            if (!Schema::hasColumn('tray_histories', 'old_jumlah_telur')) {
                $table->integer('old_jumlah_telur')->nullable()->after('old_nama_tray');
            }

            if (!Schema::hasColumn('tray_histories', 'old_keterangan')) {
                $table->text('old_keterangan')->nullable()->after('old_jumlah_telur');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tray_histories', function (Blueprint $table) {
            if (Schema::hasColumn('tray_histories', 'old_keterangan')) {
                $table->dropColumn('old_keterangan');
            }

            if (Schema::hasColumn('tray_histories', 'old_jumlah_telur')) {
                $table->dropColumn('old_jumlah_telur');
            }

            if (Schema::hasColumn('tray_histories', 'old_nama_tray')) {
                $table->dropColumn('old_nama_tray');
            }
        });
    }
};
