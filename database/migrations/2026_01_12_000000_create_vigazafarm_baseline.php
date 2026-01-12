<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->createPengguna();
        $this->createKandang();
        $this->createBatchProduksi();
        $this->createStokPakan();
        $this->createFeedVitaminItems();
        $this->createPenetasan();
        $this->createPembesaran();
        $this->createProduksi();
        $this->createPencatatanProduksi();
        $this->createPakan();
        $this->createKematian();
        $this->createMonitoringLingkungan();
        $this->createParameterStandar();
        $this->createLaporanHarian();
        $this->createBeratSampling();
        $this->createFeedHistories();
        $this->createTrayHistories();
        $this->createKesehatan();
        $this->createMlTables();

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('vf_ml_outputs');
        Schema::dropIfExists('vf_ml_runs');
        Schema::dropIfExists('vf_kesehatan');
        Schema::dropIfExists('vf_tray_histories');
        Schema::dropIfExists('vf_feed_histories');
        Schema::dropIfExists('vf_berat_sampling');
        Schema::dropIfExists('vf_laporan_harian');
        Schema::dropIfExists('vf_parameter_standar');
        Schema::dropIfExists('vf_monitoring_lingkungan');
        Schema::dropIfExists('vf_kematian');
        Schema::dropIfExists('vf_pakan');
        Schema::dropIfExists('vf_pencatatan_produksi');
        Schema::dropIfExists('vf_produksi');
        Schema::dropIfExists('vf_pembesaran');
        Schema::dropIfExists('vf_penetasan');
        Schema::dropIfExists('vf_feed_vitamin_items');
        Schema::dropIfExists('vf_stok_pakan');
        Schema::dropIfExists('vf_batch_produksi');
        Schema::dropIfExists('vf_kandang');
        Schema::dropIfExists('vf_pengguna');

        Schema::enableForeignKeyConstraints();
    }

    private function createPengguna(): void
    {
        if (Schema::hasTable('vf_pengguna')) {
            return;
        }

        Schema::create('vf_pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nama_pengguna')->unique();
            $table->string('surel')->unique();
            $table->string('nomor_telepon', 30)->nullable();
            $table->string('kata_sandi');
            $table->enum('peran', ['owner', 'operator'])->default('operator');
            $table->string('foto_profil')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamp('surel_terverifikasi_pada')->nullable();
            $table->string('token_ingat', 100)->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
        });
    }

    private function createKandang(): void
    {
        if (Schema::hasTable('vf_kandang')) {
            return;
        }

        Schema::create('vf_kandang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kandang', 50)->unique();
            $table->string('nama_kandang', 100);
            $table->integer('kapasitas_maksimal');
            $table->enum('tipe_kandang', ['penetasan', 'pembesaran', 'produksi', 'karantina']);
            $table->enum('status', ['aktif', 'maintenance', 'kosong'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createBatchProduksi(): void
    {
        if (Schema::hasTable('vf_batch_produksi')) {
            return;
        }

        Schema::create('vf_batch_produksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_batch', 50)->unique();
            $table->foreignId('kandang_id')->constrained('vf_kandang')->restrictOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir')->nullable();
            $table->integer('jumlah_awal');
            $table->integer('jumlah_saat_ini')->nullable();
            $table->enum('fase', ['DOQ', 'grower', 'layer', 'afkir'])->default('DOQ');
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createStokPakan(): void
    {
        if (Schema::hasTable('vf_stok_pakan')) {
            return;
        }

        Schema::create('vf_stok_pakan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pakan', 50)->unique();
            $table->string('nama_pakan', 100);
            $table->string('jenis_pakan', 50);
            $table->string('merek', 100)->nullable();
            $table->decimal('harga_per_kg', 12, 2);
            $table->decimal('stok_kg', 12, 2)->default(0);
            $table->integer('stok_karung')->default(0);
            $table->decimal('berat_per_karung', 8, 2)->default(50);
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->string('supplier', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createFeedVitaminItems(): void
    {
        if (Schema::hasTable('vf_feed_vitamin_items')) {
            return;
        }

        Schema::create('vf_feed_vitamin_items', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['pakan', 'vitamin']);
            $table->string('name');
            $table->decimal('price', 14, 2)->default(0);
            $table->string('unit', 50)->default('kg');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category', 'name']);
        });
    }

    private function createPenetasan(): void
    {
        if (Schema::hasTable('vf_penetasan')) {
            return;
        }

        Schema::create('vf_penetasan', function (Blueprint $table) {
            $table->id();
            $table->string('batch', 50)->nullable();
            $table->foreignId('kandang_id')->nullable()->constrained('vf_kandang')->nullOnDelete();
            $table->date('tanggal_simpan_telur');
            $table->date('estimasi_tanggal_menetas')->nullable();
            $table->date('tanggal_masuk_hatcher')->nullable();
            $table->integer('jumlah_telur');
            $table->date('tanggal_menetas')->nullable();
            $table->integer('jumlah_menetas')->nullable();
            $table->integer('jumlah_doc')->nullable();
            $table->decimal('suhu_penetasan', 5, 2)->nullable();
            $table->decimal('kelembaban_penetasan', 5, 2)->nullable();
            $table->integer('telur_tidak_fertil')->nullable();
            $table->decimal('persentase_tetas', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['proses', 'selesai', 'gagal'])->default('proses');
            $table->enum('fase_penetasan', ['setter', 'hatcher'])->default('setter');
            $table->integer('doc_ditransfer')->default(0);
            $table->integer('telur_infertil_ditransfer')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();

            $table->index(['kandang_id', 'tanggal_simpan_telur']);
            $table->index(['status', 'fase_penetasan']);
        });
    }

    private function createPembesaran(): void
    {
        if (Schema::hasTable('vf_pembesaran')) {
            return;
        }

        Schema::create('vf_pembesaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->nullable()->constrained('vf_kandang')->nullOnDelete();
            $table->foreignId('batch_produksi_id')->nullable()->constrained('vf_batch_produksi')->nullOnDelete();
            $table->foreignId('penetasan_id')->nullable()->constrained('vf_penetasan')->nullOnDelete();
            $table->date('tanggal_masuk');
            $table->integer('jumlah_anak_ayam');
            $table->string('jenis_kelamin', 20)->default('campuran');
            $table->string('status_batch', 50)->default('Aktif');
            $table->date('tanggal_selesai')->nullable();
            $table->date('tanggal_siap')->nullable();
            $table->integer('jumlah_siap')->nullable();
            $table->integer('umur_hari')->nullable();
            $table->decimal('berat_rata_rata', 8, 2)->nullable();
            $table->decimal('target_berat_akhir', 8, 2)->nullable();
            $table->text('kondisi_doc')->nullable();
            $table->text('catatan')->nullable();
            $table->integer('indukan_ditransfer')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();

            $table->index(['batch_produksi_id', 'status_batch']);
        });
    }

    private function createProduksi(): void
    {
        if (Schema::hasTable('vf_produksi')) {
            return;
        }

        Schema::create('vf_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->nullable()->constrained('vf_kandang')->nullOnDelete();
            $table->foreignId('batch_produksi_id')->nullable()->constrained('vf_batch_produksi')->nullOnDelete();
            $table->foreignId('penetasan_id')->nullable()->constrained('vf_penetasan')->nullOnDelete();
            $table->foreignId('pembesaran_id')->nullable()->constrained('vf_pembesaran')->nullOnDelete();
            $table->foreignId('produksi_sumber_id')->nullable()->constrained('vf_produksi')->nullOnDelete();
            $table->string('tipe_produksi', 30)->nullable();
            $table->string('jenis_input', 50)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->date('tanggal')->nullable();
            $table->integer('jumlah_telur')->nullable();
            $table->integer('jumlah_indukan')->nullable();
            $table->integer('jumlah_jantan')->nullable();
            $table->integer('jumlah_betina')->nullable();
            $table->integer('umur_mulai_produksi')->nullable();
            $table->decimal('berat_rata_rata', 8, 2)->nullable();
            $table->decimal('berat_rata_telur', 8, 2)->nullable();
            $table->decimal('persentase_fertil', 6, 2)->nullable();
            $table->decimal('harga_per_pcs', 14, 2)->nullable();
            $table->decimal('harga_per_kg', 14, 2)->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();

            $table->index(['batch_produksi_id', 'status']);
        });
    }

    private function createPencatatanProduksi(): void
    {
        if (Schema::hasTable('vf_pencatatan_produksi')) {
            return;
        }

        Schema::create('vf_pencatatan_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_id')->constrained('vf_produksi')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah_produksi');
            $table->string('kualitas', 50)->nullable();
            $table->decimal('berat_rata_rata', 8, 2)->nullable();
            $table->decimal('harga_per_unit', 14, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
        });
    }

    private function createPakan(): void
    {
        if (Schema::hasTable('vf_pakan')) {
            return;
        }

        Schema::create('vf_pakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_id')->nullable()->constrained('vf_produksi')->nullOnDelete();
            $table->foreignId('stok_pakan_id')->nullable()->constrained('vf_stok_pakan')->nullOnDelete();
            $table->foreignId('feed_item_id')->nullable()->constrained('vf_feed_vitamin_items')->nullOnDelete();
            $table->foreignId('batch_produksi_id')->nullable()->constrained('vf_batch_produksi')->nullOnDelete();
            $table->date('tanggal');
            $table->decimal('jumlah_kg', 10, 2)->nullable();
            $table->decimal('sisa_pakan_kg', 10, 2)->nullable();
            $table->integer('jumlah_karung')->nullable();
            $table->decimal('harga_per_kg', 12, 2)->nullable();
            $table->decimal('total_biaya', 14, 2)->nullable();
            $table->foreignId('pengguna_id')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();

            $table->index(['batch_produksi_id', 'tanggal']);
        });
    }

    private function createKematian(): void
    {
        if (Schema::hasTable('vf_kematian')) {
            return;
        }

        Schema::create('vf_kematian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_id')->nullable()->constrained('vf_produksi')->nullOnDelete();
            $table->foreignId('batch_produksi_id')->nullable()->constrained('vf_batch_produksi')->nullOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah');
            $table->enum('penyebab', ['penyakit', 'stress', 'kecelakaan', 'usia', 'tidak_diketahui'])->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('pengguna_id')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();

            $table->index(['batch_produksi_id', 'tanggal']);
        });
    }

    private function createMonitoringLingkungan(): void
    {
        if (Schema::hasTable('vf_monitoring_lingkungan')) {
            return;
        }

        Schema::create('vf_monitoring_lingkungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->constrained('vf_kandang')->cascadeOnDelete();
            $table->foreignId('batch_produksi_id')->nullable()->constrained('vf_batch_produksi')->nullOnDelete();
            $table->dateTime('waktu_pencatatan');
            $table->decimal('suhu', 5, 2)->nullable();
            $table->decimal('kelembaban', 5, 2)->nullable();
            $table->decimal('intensitas_cahaya', 8, 2)->nullable();
            $table->string('kondisi_ventilasi', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('pengguna_id')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();

            $table->index(['kandang_id', 'batch_produksi_id']);
            $table->index('waktu_pencatatan');
        });
    }

    private function createParameterStandar(): void
    {
        if (Schema::hasTable('vf_parameter_standar')) {
            return;
        }

        Schema::create('vf_parameter_standar', function (Blueprint $table) {
            $table->id();
            $table->enum('fase', ['DOQ', 'grower', 'layer']);
            $table->string('parameter', 100);
            $table->decimal('nilai_minimal', 10, 2)->nullable();
            $table->decimal('nilai_optimal', 10, 2)->nullable();
            $table->decimal('nilai_maksimal', 10, 2)->nullable();
            $table->string('satuan', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['fase', 'parameter']);
        });
    }

    private function createLaporanHarian(): void
    {
        if (Schema::hasTable('vf_laporan_harian')) {
            return;
        }

        Schema::create('vf_laporan_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_produksi_id')->constrained('vf_batch_produksi')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah_burung');
            $table->integer('produksi_telur')->default(0);
            $table->integer('input_telur')->default(0);
            $table->integer('telur_rusak')->default(0);
            $table->string('nama_tray', 120)->nullable();
            $table->text('keterangan_tray')->nullable();
            $table->integer('jumlah_kematian')->default(0);
            $table->enum('jenis_kelamin_kematian', ['jantan', 'betina', 'campuran'])->nullable();
            $table->text('keterangan_kematian')->nullable();
            $table->decimal('konsumsi_pakan_kg', 10, 2)->default(0);
            $table->decimal('sisa_pakan_kg', 10, 2)->nullable();
            $table->decimal('sisa_tray_bal', 8, 2)->nullable();
            $table->integer('sisa_tray_lembar')->nullable();
            $table->decimal('sisa_vitamin_liter', 8, 2)->nullable();
            $table->decimal('vitamin_terpakai', 8, 3)->nullable();
            $table->decimal('harga_pakan_per_kg', 12, 2)->nullable();
            $table->decimal('biaya_pakan_harian', 14, 2)->nullable();
            $table->decimal('harga_vitamin_per_liter', 12, 2)->nullable();
            $table->decimal('biaya_vitamin_harian', 14, 2)->nullable();
            $table->integer('sisa_telur')->nullable();
            $table->integer('penjualan_telur_butir')->nullable();
            $table->integer('penjualan_puyuh_ekor')->nullable();
            $table->decimal('pendapatan_harian', 12, 2)->nullable();
            $table->unsignedBigInteger('tray_penjualan_id')->nullable();
            $table->decimal('harga_per_butir', 8, 2)->nullable();
            $table->string('nama_tray_penjualan')->nullable();
            $table->decimal('fcr', 5, 2)->nullable();
            $table->decimal('hen_day_production', 5, 2)->nullable();
            $table->decimal('mortalitas_kumulatif', 5, 2)->nullable();
            $table->text('catatan_kejadian')->nullable();
            $table->boolean('tampilkan_di_histori')->default(true);
            $table->foreignId('pengguna_id')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['batch_produksi_id', 'tanggal']);
            $table->index('tanggal');
        });
    }

    private function createBeratSampling(): void
    {
        if (Schema::hasTable('vf_berat_sampling')) {
            return;
        }

        Schema::create('vf_berat_sampling', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_produksi_id')->constrained('vf_batch_produksi')->cascadeOnDelete();
            $table->date('tanggal_sampling');
            $table->integer('umur_hari')->nullable();
            $table->decimal('berat_rata_rata', 8, 2);
            $table->integer('jumlah_sampel')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('pengguna_id')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
        });
    }

    private function createFeedHistories(): void
    {
        if (Schema::hasTable('vf_feed_histories')) {
            return;
        }

        Schema::create('vf_feed_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_produksi_id')->nullable();
            $table->foreignId('stok_pakan_id')->nullable()->constrained('vf_stok_pakan')->nullOnDelete();
            $table->foreignId('feed_item_id')->nullable()->constrained('vf_feed_vitamin_items')->nullOnDelete();
            $table->date('tanggal')->nullable();
            $table->integer('jumlah_karung_sisa')->default(0);
            $table->decimal('sisa_pakan_kg', 10, 2)->default(0);
            $table->string('keterangan', 255)->nullable();
            $table->foreignId('pengguna_id')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->timestamps();

            $table->index('batch_produksi_id');
            $table->index('tanggal');
        });
    }

    private function createTrayHistories(): void
    {
        if (Schema::hasTable('vf_tray_histories')) {
            return;
        }

        Schema::create('vf_tray_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_id')->constrained('vf_produksi')->cascadeOnDelete();
            $table->foreignId('laporan_harian_id')->nullable()->constrained('vf_laporan_harian')->nullOnDelete();
            $table->enum('action', ['created', 'updated', 'deleted']);
            $table->string('nama_tray', 120)->nullable();
            $table->date('tanggal')->nullable();
            $table->integer('jumlah_telur')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('old_nama_tray', 120)->nullable();
            $table->integer('old_jumlah_telur')->nullable();
            $table->text('old_keterangan')->nullable();
            $table->foreignId('pengguna_id')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->timestamps();
        });
    }

    private function createKesehatan(): void
    {
        if (Schema::hasTable('vf_kesehatan')) {
            return;
        }

        Schema::create('vf_kesehatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_produksi_id')->constrained('vf_batch_produksi')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('tipe_kegiatan', ['vaksinasi', 'pengobatan', 'pemeriksaan_rutin', 'karantina', 'vitamin']);
            $table->string('nama_vaksin_obat', 100)->nullable();
            $table->integer('jumlah_burung')->nullable();
            $table->foreignId('kandang_tujuan_id')->nullable()->constrained('vf_kandang')->nullOnDelete();
            $table->boolean('karantina_dikembalikan')->default(false);
            $table->timestamp('karantina_dikembalikan_pada')->nullable();
            $table->foreignId('feed_vitamin_item_id')->nullable()->constrained('vf_feed_vitamin_items')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->decimal('biaya', 14, 2)->nullable();
            $table->string('petugas', 100)->nullable();
            $table->foreignId('pengguna_id')->nullable()->constrained('vf_pengguna')->nullOnDelete();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();

            $table->index(['batch_produksi_id', 'tanggal']);
        });
    }

    private function createMlTables(): void
    {
        if (!Schema::hasTable('vf_ml_runs')) {
            Schema::create('vf_ml_runs', function (Blueprint $table) {
                $table->id();
                $table->string('status', 20)->default('pending');
                $table->string('label', 100)->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->text('error_message')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('vf_ml_outputs')) {
            Schema::create('vf_ml_outputs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('run_id')->nullable()->constrained('vf_ml_runs')->nullOnDelete();
                $table->string('type', 40);
                $table->string('entity_type', 40)->nullable();
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->date('tanggal_prediksi')->nullable();
                $table->unsignedSmallInteger('horizon')->default(0);
                $table->decimal('nilai', 15, 2)->nullable();
                $table->decimal('lower', 15, 2)->nullable();
                $table->decimal('upper', 15, 2)->nullable();
                $table->decimal('score', 10, 4)->nullable();
                $table->string('status_flag', 20)->default('normal');
                $table->json('top_features')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['type', 'tanggal_prediksi']);
                $table->index(['entity_type', 'entity_id']);
            });
        }
    }
};
