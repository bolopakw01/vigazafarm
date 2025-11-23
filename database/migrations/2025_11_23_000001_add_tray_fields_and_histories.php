<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_harian', 'nama_tray')) {
                $table->string('nama_tray', 120)->nullable()->after('produksi_telur');
            }

            if (!Schema::hasColumn('laporan_harian', 'keterangan_tray')) {
                $table->text('keterangan_tray')->nullable()->after('nama_tray');
            }
        });

        if (!Schema::hasTable('tray_histories')) {
            Schema::create('tray_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('produksi_id')->constrained('produksi')->onDelete('cascade');
                $table->foreignId('laporan_harian_id')->nullable()->constrained('laporan_harian')->nullOnDelete();
                $table->enum('action', ['created', 'updated', 'deleted']);
                $table->string('nama_tray', 120)->nullable();
                $table->date('tanggal')->nullable();
                $table->integer('jumlah_telur')->nullable();
                $table->text('keterangan')->nullable();
                // Old values for change tracking
                $table->string('old_nama_tray', 120)->nullable();
                $table->integer('old_jumlah_telur')->nullable();
                $table->text('old_keterangan')->nullable();
                $table->foreignId('pengguna_id')->nullable()->constrained('pengguna')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tray_histories')) {
            Schema::dropIfExists('tray_histories');
        }

        Schema::table('laporan_harian', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_harian', 'keterangan_tray')) {
                $table->dropColumn('keterangan_tray');
            }

            if (Schema::hasColumn('laporan_harian', 'nama_tray')) {
                $table->dropColumn('nama_tray');
            }
        });
    }
};
