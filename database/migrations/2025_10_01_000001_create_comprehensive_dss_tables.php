<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migration untuk Decision Support System Terintegrasi
     * Monitoring Agribisnis Burung Puyuh
     */
    public function up(): void
    {
        // 1. Tabel Kandang (Housing Management)
        Schema::create('kandang', function (Blueprint $table) {
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

        // 2. Tabel Batch/Periode Produksi (Production Batch Management)
        Schema::create('batch_produksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_batch', 50)->unique();
            $table->foreignId('kandang_id')->constrained('kandang')->onDelete('restrict');
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir')->nullable();
            $table->integer('jumlah_awal');
            $table->integer('jumlah_saat_ini')->nullable();
            $table->enum('fase', ['DOC', 'grower', 'layer', 'afkir'])->default('DOC');
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Tabel Stok Pakan (Feed Inventory)
        Schema::create('stok_pakan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pakan', 50)->unique();
            $table->string('nama_pakan', 100);
            $table->string('jenis_pakan', 50); // Starter, Grower, Layer
            $table->string('merek', 100)->nullable();
            $table->decimal('harga_per_kg', 10, 2);
            $table->decimal('stok_kg', 10, 2)->default(0);
            $table->integer('stok_karung')->default(0);
            $table->decimal('berat_per_karung', 8, 2)->default(50); // kg
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->string('supplier', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Tabel Transaksi Pakan (Feed Transactions)
        Schema::create('transaksi_pakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_pakan_id')->constrained('stok_pakan')->onDelete('restrict');
            $table->foreignId('batch_produksi_id')->nullable()->constrained('batch_produksi')->onDelete('set null');
            $table->enum('tipe_transaksi', ['pembelian', 'penggunaan', 'penyesuaian', 'pengembalian']);
            $table->date('tanggal');
            $table->decimal('jumlah_kg', 10, 2);
            $table->integer('jumlah_karung')->nullable();
            $table->decimal('harga_total', 12, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('restrict');
            $table->timestamps();
        });

        // 5. Tabel Kesehatan & Vaksinasi (Health & Vaccination)
        Schema::create('kesehatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_produksi_id')->constrained('batch_produksi')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('tipe_kegiatan', ['vaksinasi', 'pengobatan', 'pemeriksaan_rutin', 'karantina']);
            $table->string('nama_vaksin_obat', 100)->nullable();
            $table->integer('jumlah_burung')->nullable();
            $table->text('gejala')->nullable();
            $table->text('diagnosa')->nullable();
            $table->text('tindakan')->nullable();
            $table->decimal('biaya', 10, 2)->nullable();
            $table->string('petugas', 100)->nullable();
            $table->timestamps();
        });

        // 6. Tabel Keuangan (Financial Management)
        Schema::create('keuangan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('kategori', ['pemasukan', 'pengeluaran']);
            $table->enum('jenis', [
                'penjualan_telur',
                'penjualan_burung',
                'pembelian_pakan',
                'pembelian_bibit',
                'pembelian_obat',
                'pembelian_peralatan',
                'gaji_karyawan',
                'listrik_air',
                'maintenance',
                'lainnya'
            ]);
            $table->decimal('jumlah', 12, 2);
            $table->foreignId('batch_produksi_id')->nullable()->constrained('batch_produksi')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->string('nomor_bukti', 50)->nullable();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Tabel Penjualan Telur (Egg Sales)
        Schema::create('penjualan_telur', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('batch_produksi_id')->nullable()->constrained('batch_produksi')->onDelete('set null');
            $table->integer('jumlah_butir');
            $table->decimal('harga_per_butir', 8, 2);
            $table->decimal('total_harga', 12, 2);
            $table->string('pembeli', 100)->nullable();
            $table->string('kontak_pembeli', 50)->nullable();
            $table->enum('status_pembayaran', ['lunas', 'belum_lunas', 'cicilan'])->default('lunas');
            $table->text('catatan')->nullable();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('restrict');
            $table->timestamps();
        });

        // 8. Tabel Penjualan Burung (Quail Sales)
        Schema::create('penjualan_burung', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('batch_produksi_id')->nullable()->constrained('batch_produksi')->onDelete('set null');
            $table->enum('kategori', ['DOC', 'grower', 'layer', 'afkir', 'jantan']);
            $table->integer('jumlah_ekor');
            $table->decimal('berat_rata_rata', 8, 2)->nullable(); // gram
            $table->decimal('harga_per_ekor', 10, 2);
            $table->decimal('total_harga', 12, 2);
            $table->string('pembeli', 100)->nullable();
            $table->string('kontak_pembeli', 50)->nullable();
            $table->enum('status_pembayaran', ['lunas', 'belum_lunas', 'cicilan'])->default('lunas');
            $table->text('catatan')->nullable();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('restrict');
            $table->timestamps();
        });

        // 9. Tabel Monitoring Lingkungan (Environment Monitoring)
        Schema::create('monitoring_lingkungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->constrained('kandang')->onDelete('cascade');
            $table->foreignId('batch_produksi_id')->nullable()->constrained('batch_produksi')->onDelete('set null');
            $table->dateTime('waktu_pencatatan');
            $table->decimal('suhu', 5, 2)->nullable(); // Celsius
            $table->decimal('kelembaban', 5, 2)->nullable(); // Percentage
            $table->decimal('intensitas_cahaya', 8, 2)->nullable(); // Lux
            $table->string('kondisi_ventilasi', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 10. Tabel Parameter Standar (Standard Parameters for DSS)
        Schema::create('parameter_standar', function (Blueprint $table) {
            $table->id();
            $table->enum('fase', ['DOC', 'grower', 'layer']);
            $table->string('parameter', 100); // misal: konsumsi_pakan_harian, produksi_telur_target
            $table->decimal('nilai_minimal', 10, 2)->nullable();
            $table->decimal('nilai_optimal', 10, 2)->nullable();
            $table->decimal('nilai_maksimal', 10, 2)->nullable();
            $table->string('satuan', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 11. Tabel Analisis & Rekomendasi (DSS Recommendations)
        Schema::create('analisis_rekomendasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_produksi_id')->constrained('batch_produksi')->onDelete('cascade');
            $table->date('tanggal_analisis');
            $table->string('jenis_analisis', 100); // FCR, Mortalitas, Produktivitas, dll
            $table->decimal('nilai_aktual', 10, 2)->nullable();
            $table->decimal('nilai_standar', 10, 2)->nullable();
            $table->enum('status', ['baik', 'perhatian', 'kritis'])->default('baik');
            $table->text('analisis')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi', 'urgent'])->default('sedang');
            $table->date('target_tindakan')->nullable();
            $table->enum('status_tindakan', ['pending', 'dalam_proses', 'selesai', 'diabaikan'])->default('pending');
            $table->timestamps();
        });

        // 12. Tabel Laporan Harian (Daily Report Summary)
        Schema::create('laporan_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_produksi_id')->constrained('batch_produksi')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('jumlah_burung');
            $table->integer('produksi_telur')->default(0);
            $table->integer('jumlah_kematian')->default(0);
            $table->decimal('konsumsi_pakan_kg', 10, 2)->default(0);
            $table->decimal('fcr', 5, 2)->nullable(); // Feed Conversion Ratio
            $table->decimal('hen_day_production', 5, 2)->nullable(); // %
            $table->decimal('mortalitas_kumulatif', 5, 2)->nullable(); // %
            $table->text('catatan_kejadian')->nullable();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('restrict');
            $table->timestamps();
        });

        // 13. Tabel Alert/Notifikasi (Alert System)
        Schema::create('alert', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe_alert', ['stok_pakan', 'kesehatan', 'produktivitas', 'keuangan', 'lingkungan', 'lainnya']);
            $table->enum('tingkat_urgency', ['info', 'warning', 'critical']);
            $table->string('judul', 200);
            $table->text('pesan');
            $table->foreignId('batch_produksi_id')->nullable()->constrained('batch_produksi')->onDelete('cascade');
            $table->foreignId('kandang_id')->nullable()->constrained('kandang')->onDelete('cascade');
            $table->boolean('sudah_dibaca')->default(false);
            $table->dateTime('waktu_dibaca')->nullable();
            $table->foreignId('pengguna_id')->nullable()->constrained('pengguna')->onDelete('set null');
            $table->timestamps();
        });

        // Update tabel penetasan dengan relasi kandang
        Schema::table('penetasan', function (Blueprint $table) {
            $table->foreignId('kandang_id')->nullable()->after('id')->constrained('kandang')->onDelete('set null');
            $table->decimal('suhu_penetasan', 5, 2)->nullable()->after('jumlah_doc');
            $table->decimal('kelembaban_penetasan', 5, 2)->nullable()->after('suhu_penetasan');
            $table->integer('telur_tidak_fertil')->nullable()->after('jumlah_doc');
            $table->decimal('persentase_tetas', 5, 2)->nullable()->after('telur_tidak_fertil');
            $table->text('catatan')->nullable()->after('persentase_tetas');
        });

        // Update tabel pembesaran dengan relasi kandang dan batch
        Schema::table('pembesaran', function (Blueprint $table) {
            $table->foreignId('kandang_id')->nullable()->after('id')->constrained('kandang')->onDelete('set null');
            $table->foreignId('batch_produksi_id')->nullable()->after('kandang_id')->constrained('batch_produksi')->onDelete('set null');
            $table->integer('umur_hari')->nullable()->after('jumlah_siap');
            $table->decimal('berat_rata_rata', 8, 2)->nullable()->after('umur_hari'); // gram
            $table->text('catatan')->nullable()->after('berat_rata_rata');
        });

        // Update tabel produksi dengan relasi kandang dan batch
        Schema::table('produksi', function (Blueprint $table) {
            $table->foreignId('kandang_id')->nullable()->after('id')->constrained('kandang')->onDelete('set null');
            $table->foreignId('batch_produksi_id')->nullable()->after('kandang_id')->constrained('batch_produksi')->onDelete('set null');
            $table->integer('umur_mulai_produksi')->nullable()->after('jumlah_indukan'); // hari
            $table->enum('status', ['aktif', 'selesai'])->default('aktif')->after('tanggal_akhir');
            $table->text('catatan')->nullable()->after('status');
        });

        // Update tabel pakan dengan informasi lebih detail
        Schema::table('pakan', function (Blueprint $table) {
            $table->foreignId('stok_pakan_id')->nullable()->after('produksi_id')->constrained('stok_pakan')->onDelete('set null');
            $table->foreignId('batch_produksi_id')->nullable()->after('stok_pakan_id')->constrained('batch_produksi')->onDelete('set null');
            $table->decimal('harga_per_kg', 10, 2)->nullable()->after('jumlah_karung');
            $table->decimal('total_biaya', 12, 2)->nullable()->after('harga_per_kg');
        });

        // Update tabel telur dengan informasi kualitas
        Schema::table('telur', function (Blueprint $table) {
            $table->foreignId('batch_produksi_id')->nullable()->after('produksi_id')->constrained('batch_produksi')->onDelete('set null');
            $table->integer('telur_grade_a')->nullable()->after('jumlah');
            $table->integer('telur_grade_b')->nullable()->after('telur_grade_a');
            $table->integer('telur_grade_c')->nullable()->after('telur_grade_b');
            $table->integer('telur_retak')->nullable()->after('telur_grade_c');
            $table->decimal('berat_rata_rata', 5, 2)->nullable()->after('telur_retak'); // gram
        });

        // Update tabel kematian dengan penyebab
        Schema::table('kematian', function (Blueprint $table) {
            $table->foreignId('batch_produksi_id')->nullable()->after('produksi_id')->constrained('batch_produksi')->onDelete('set null');
            $table->enum('penyebab', ['penyakit', 'stress', 'kecelakaan', 'usia', 'tidak_diketahui'])->nullable()->after('jumlah');
            $table->text('keterangan')->nullable()->after('penyebab');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first
        Schema::table('kematian', function (Blueprint $table) {
            $table->dropForeign(['batch_produksi_id']);
            $table->dropColumn(['batch_produksi_id', 'penyebab', 'keterangan']);
        });

        Schema::table('telur', function (Blueprint $table) {
            $table->dropForeign(['batch_produksi_id']);
            $table->dropColumn(['batch_produksi_id', 'telur_grade_a', 'telur_grade_b', 'telur_grade_c', 'telur_retak', 'berat_rata_rata']);
        });

        Schema::table('pakan', function (Blueprint $table) {
            $table->dropForeign(['stok_pakan_id', 'batch_produksi_id']);
            $table->dropColumn(['stok_pakan_id', 'batch_produksi_id', 'harga_per_kg', 'total_biaya']);
        });

        Schema::table('produksi', function (Blueprint $table) {
            $table->dropForeign(['kandang_id', 'batch_produksi_id']);
            $table->dropColumn(['kandang_id', 'batch_produksi_id', 'umur_mulai_produksi', 'status', 'catatan']);
        });

        Schema::table('pembesaran', function (Blueprint $table) {
            $table->dropForeign(['kandang_id', 'batch_produksi_id']);
            $table->dropColumn(['kandang_id', 'batch_produksi_id', 'umur_hari', 'berat_rata_rata', 'catatan']);
        });

        Schema::table('penetasan', function (Blueprint $table) {
            $table->dropForeign(['kandang_id']);
            $table->dropColumn(['kandang_id', 'suhu_penetasan', 'kelembaban_penetasan', 'telur_tidak_fertil', 'persentase_tetas', 'catatan']);
        });

        // Drop new tables
        Schema::dropIfExists('alert');
        Schema::dropIfExists('laporan_harian');
        Schema::dropIfExists('analisis_rekomendasi');
        Schema::dropIfExists('parameter_standar');
        Schema::dropIfExists('monitoring_lingkungan');
        Schema::dropIfExists('penjualan_burung');
        Schema::dropIfExists('penjualan_telur');
        Schema::dropIfExists('keuangan');
        Schema::dropIfExists('kesehatan');
        Schema::dropIfExists('transaksi_pakan');
        Schema::dropIfExists('stok_pakan');
        Schema::dropIfExists('batch_produksi');
        Schema::dropIfExists('kandang');
    }
};
