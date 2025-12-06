<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration untuk menambahkan kolom dan relasi yang dibutuhkan
     * untuk sistem pencatatan pembesaran
     */
    public function up(): void
    {
        // Update tabel pembesaran - tambah kolom penetasan_id jika belum ada
        if (!Schema::hasColumn('pembesaran', 'penetasan_id')) {
            Schema::table('pembesaran', function (Blueprint $table) {
                $table->foreignId('penetasan_id')->nullable()->after('batch_produksi_id')
                    ->constrained('penetasan')->onDelete('set null');
            });
        }

        // Update tabel pembesaran - tambah kolom kondisi_doc
        if (!Schema::hasColumn('pembesaran', 'kondisi_doc')) {
            Schema::table('pembesaran', function (Blueprint $table) {
                $table->string('kondisi_doc', 100)->nullable()->after('berat_rata_rata')
                    ->comment('Kondisi DOQ saat masuk: Baik, Lemah, dll');
            });
        }

        // Pastikan tabel pakan sudah ada relasi yang benar
        if (!Schema::hasTable('pakan')) {
            Schema::create('pakan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('produksi_id')->nullable()->constrained('produksi')->onDelete('cascade');
                $table->foreignId('stok_pakan_id')->nullable()->constrained('stok_pakan')->onDelete('set null');
                $table->foreignId('batch_produksi_id')->nullable()->constrained('batch_produksi')->onDelete('set null');
                $table->date('tanggal');
                $table->decimal('jumlah_kg', 8, 2)->nullable();
                $table->integer('jumlah_karung')->nullable();
                $table->decimal('harga_per_kg', 10, 2)->nullable();
                $table->decimal('total_biaya', 12, 2)->nullable();
                $table->timestamp('dibuat_pada')->useCurrent();
                $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
                
                $table->index('tanggal');
                $table->index('batch_produksi_id');
            });
        }

        // Pastikan tabel kematian sudah ada
        if (!Schema::hasTable('kematian')) {
            Schema::create('kematian', function (Blueprint $table) {
                $table->id();
                $table->foreignId('produksi_id')->nullable()->constrained('produksi')->onDelete('cascade');
                $table->foreignId('batch_produksi_id')->nullable()->constrained('batch_produksi')->onDelete('set null');
                $table->date('tanggal');
                $table->integer('jumlah');
                $table->enum('penyebab', ['penyakit', 'stress', 'kecelakaan', 'usia', 'tidak_diketahui'])->default('tidak_diketahui');
                $table->text('keterangan')->nullable();
                $table->timestamp('dibuat_pada')->useCurrent();
                $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
                
                $table->index('tanggal');
                $table->index('batch_produksi_id');
            });
        }

        // Pastikan tabel laporan_harian sudah ada
        if (!Schema::hasTable('laporan_harian')) {
            Schema::create('laporan_harian', function (Blueprint $table) {
                $table->id();
                $table->foreignId('batch_produksi_id')->constrained('batch_produksi')->onDelete('cascade');
                $table->date('tanggal');
                $table->integer('jumlah_burung')->comment('Populasi burung hari ini');
                $table->integer('produksi_telur')->default(0)->comment('Jumlah telur hari ini');
                $table->integer('jumlah_kematian')->default(0)->comment('Jumlah kematian hari ini');
                $table->decimal('konsumsi_pakan_kg', 10, 2)->default(0)->comment('Konsumsi pakan hari ini (kg)');
                $table->decimal('fcr', 5, 2)->nullable()->comment('Feed Conversion Ratio');
                $table->decimal('hen_day_production', 5, 2)->nullable()->comment('HDP (%)');
                $table->decimal('mortalitas_kumulatif', 5, 2)->nullable()->comment('Mortalitas kumulatif (%)');
                $table->text('catatan_kejadian')->nullable()->comment('Catatan kejadian khusus hari ini');
                $table->foreignId('pengguna_id')->nullable()->constrained('pengguna')->onDelete('set null');
                $table->timestamp('dibuat_pada')->useCurrent();
                $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
                
                $table->unique(['batch_produksi_id', 'tanggal'], 'unique_batch_tanggal');
                $table->index('tanggal');
            });
        }

        // Pastikan tabel monitoring_lingkungan sudah ada
        if (!Schema::hasTable('monitoring_lingkungan')) {
            Schema::create('monitoring_lingkungan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kandang_id')->constrained('kandang')->onDelete('cascade');
                $table->foreignId('batch_produksi_id')->nullable()->constrained('batch_produksi')->onDelete('set null');
                $table->datetime('waktu_pencatatan');
                $table->decimal('suhu', 5, 2)->nullable()->comment('Suhu dalam Celsius');
                $table->decimal('kelembaban', 5, 2)->nullable()->comment('Kelembaban dalam %');
                $table->decimal('intensitas_cahaya', 8, 2)->nullable()->comment('Intensitas cahaya dalam lux');
                $table->string('kondisi_ventilasi', 50)->nullable()->comment('Baik, Cukup, Kurang');
                $table->text('catatan')->nullable();
                $table->timestamp('dibuat_pada')->useCurrent();
                $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
                
                $table->index('waktu_pencatatan');
                $table->index('kandang_id');
                $table->index('batch_produksi_id');
            });
        }

        // Pastikan tabel kesehatan sudah ada
        if (!Schema::hasTable('kesehatan')) {
            Schema::create('kesehatan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('batch_produksi_id')->constrained('batch_produksi')->onDelete('cascade');
                $table->date('tanggal');
                $table->enum('tipe_kegiatan', ['vaksinasi', 'pengobatan', 'pemeriksaan_rutin', 'karantina', 'vitamin']);
                $table->string('nama_vaksin_obat', 100)->nullable();
                $table->integer('jumlah_burung')->nullable();
                $table->text('gejala')->nullable();
                $table->text('diagnosa')->nullable();
                $table->text('tindakan')->nullable();
                $table->decimal('biaya', 10, 2)->nullable();
                $table->string('petugas', 100)->nullable();
                $table->timestamp('dibuat_pada')->useCurrent();
                $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
                
                $table->index('tanggal');
                $table->index('batch_produksi_id');
                $table->index('tipe_kegiatan');
            });
        }

        // Pastikan tabel parameter_standar sudah ada
        if (!Schema::hasTable('parameter_standar')) {
            Schema::create('parameter_standar', function (Blueprint $table) {
                $table->id();
                $table->enum('fase', ['DOQ', 'grower', 'layer']);
                $table->string('parameter', 100)->comment('Nama parameter: berat_rata_rata, suhu, kelembaban, dll');
                $table->decimal('nilai_minimal', 10, 2)->nullable();
                $table->decimal('nilai_optimal', 10, 2)->nullable();
                $table->decimal('nilai_maksimal', 10, 2)->nullable();
                $table->string('satuan', 20)->nullable()->comment('gram, celsius, %, dll');
                $table->text('keterangan')->nullable();
                $table->timestamp('dibuat_pada')->useCurrent();
                $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
                
                $table->index(['fase', 'parameter']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameter_standar');
        Schema::dropIfExists('kesehatan');
        Schema::dropIfExists('monitoring_lingkungan');
        Schema::dropIfExists('laporan_harian');
        Schema::dropIfExists('kematian');
        Schema::dropIfExists('pakan');
        
        // Rollback kolom tambahan di pembesaran
        if (Schema::hasColumn('pembesaran', 'kondisi_doc')) {
            Schema::table('pembesaran', function (Blueprint $table) {
                $table->dropColumn('kondisi_doc');
            });
        }
        
        if (Schema::hasColumn('pembesaran', 'penetasan_id')) {
            Schema::table('pembesaran', function (Blueprint $table) {
                $table->dropForeign(['penetasan_id']);
                $table->dropColumn('penetasan_id');
            });
        }
    }
};
