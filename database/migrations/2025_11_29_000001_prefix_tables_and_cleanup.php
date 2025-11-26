<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mapping of legacy tables to their new prefixed counterparts.
     */
    private array $tableMap = [
        'pengguna' => 'vf_pengguna',
        'penetasan' => 'vf_penetasan',
        'pembesaran' => 'vf_pembesaran',
        'produksi' => 'vf_produksi',
        'pakan' => 'vf_pakan',
        'telur' => 'vf_telur',
        'kematian' => 'vf_kematian',
        'kandang' => 'vf_kandang',
        'batch_produksi' => 'vf_batch_produksi',
        'stok_pakan' => 'vf_stok_pakan',
        'monitoring_lingkungan' => 'vf_monitoring_lingkungan',
        'parameter_standar' => 'vf_parameter_standar',
        'laporan_harian' => 'vf_laporan_harian',
        'berat_sampling' => 'vf_berat_sampling',
        'feed_vitamin_items' => 'vf_feed_vitamin_items',
        'tray_histories' => 'vf_tray_histories',
        'pencatatan_produksi' => 'vf_pencatatan_produksi',
        'kesehatan' => 'vf_kesehatan',
    ];

    /**
     * Tables that are no longer required and should be dropped.
     */
    private array $obsoleteTables = [
        'transaksi_pakan',
        'keuangan',
        'penjualan_telur',
        'penjualan_burung',
        'analisis_rekomendasi',
        'alert',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->tableMap as $oldName => $newName) {
            $this->renameTable($oldName, $newName);
        }

        foreach ($this->obsoleteTables as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach (array_reverse($this->tableMap) as $oldName => $newName) {
            $this->renameTable($newName, $oldName);
        }

        Schema::enableForeignKeyConstraints();

        $this->recreateObsoleteTables();
    }

    private function renameTable(string $from, string $to): void
    {
        if (!Schema::hasTable($from)) {
            return;
        }

        if ($from === $to || Schema::hasTable($to)) {
            return;
        }

        Schema::rename($from, $to);
    }

    private function recreateObsoleteTables(): void
    {
        if (!Schema::hasTable('transaksi_pakan')) {
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
        }

        if (!Schema::hasTable('keuangan')) {
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
                    'lainnya',
                ]);
                $table->decimal('jumlah', 12, 2);
                $table->foreignId('batch_produksi_id')->nullable()->constrained('batch_produksi')->onDelete('set null');
                $table->text('keterangan')->nullable();
                $table->string('nomor_bukti', 50)->nullable();
                $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('restrict');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('penjualan_telur')) {
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
        }

        if (!Schema::hasTable('penjualan_burung')) {
            Schema::create('penjualan_burung', function (Blueprint $table) {
                $table->id();
                $table->string('kode_transaksi', 50)->unique();
                $table->date('tanggal');
                $table->foreignId('batch_produksi_id')->nullable()->constrained('batch_produksi')->onDelete('set null');
                $table->enum('kategori', ['DOC', 'grower', 'layer', 'afkir', 'jantan']);
                $table->integer('jumlah_ekor');
                $table->decimal('berat_rata_rata', 8, 2)->nullable();
                $table->decimal('harga_per_ekor', 10, 2);
                $table->decimal('total_harga', 12, 2);
                $table->string('pembeli', 100)->nullable();
                $table->string('kontak_pembeli', 50)->nullable();
                $table->enum('status_pembayaran', ['lunas', 'belum_lunas', 'cicilan'])->default('lunas');
                $table->text('catatan')->nullable();
                $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('restrict');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('analisis_rekomendasi')) {
            Schema::create('analisis_rekomendasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('batch_produksi_id')->constrained('batch_produksi')->onDelete('cascade');
                $table->date('tanggal_analisis');
                $table->string('jenis_analisis', 100);
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
        }

        if (!Schema::hasTable('alert')) {
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
        }
    }
};
