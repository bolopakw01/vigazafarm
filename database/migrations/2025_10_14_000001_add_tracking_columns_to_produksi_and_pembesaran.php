<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom tracking untuk alur: Penetasan -> Pembesaran -> Produksi
     */
    public function up(): void
    {
        // Tambahkan kolom tracking di tabel pembesaran
        if (!Schema::hasColumn('pembesaran', 'penetasan_id')) {
            Schema::table('pembesaran', function (Blueprint $table) {
                $table->unsignedBigInteger('penetasan_id')->nullable()->after('batch_produksi_id');
                $table->foreign('penetasan_id')->references('id')->on('penetasan')->onDelete('set null');
                $table->index('penetasan_id');
            });
        }

        // Tambahkan kolom tracking di tabel produksi
        Schema::table('produksi', function (Blueprint $table) {
            // Tracking asal dari penetasan (telur tidak fertil langsung ke produksi)
            if (!Schema::hasColumn('produksi', 'penetasan_id')) {
                $table->unsignedBigInteger('penetasan_id')->nullable()->after('batch_produksi_id');
                $table->foreign('penetasan_id')->references('id')->on('penetasan')->onDelete('set null');
                $table->index('penetasan_id');
            }
            
            // Tracking asal dari pembesaran (indukan yang sudah siap produksi)
            if (!Schema::hasColumn('produksi', 'pembesaran_id')) {
                $table->unsignedBigInteger('pembesaran_id')->nullable()->after('penetasan_id');
                $table->foreign('pembesaran_id')->references('id')->on('pembesaran')->onDelete('set null');
                $table->index('pembesaran_id');
            }

            // Kolom untuk tracking jumlah yang ditransfer
            if (!Schema::hasColumn('produksi', 'jumlah_indukan')) {
                $table->integer('jumlah_indukan')->nullable()->after('batch_produksi_id');
            }

            if (!Schema::hasColumn('produksi', 'umur_mulai_produksi')) {
                $table->integer('umur_mulai_produksi')->nullable()->comment('Umur burung saat mulai produksi (hari)');
            }

            if (!Schema::hasColumn('produksi', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->nullable()->comment('Tanggal batch mulai produksi');
            }

            if (!Schema::hasColumn('produksi', 'tanggal_akhir')) {
                $table->date('tanggal_akhir')->nullable()->comment('Tanggal batch selesai produksi');
            }

            if (!Schema::hasColumn('produksi', 'status')) {
                $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            }
        });

        // Tambahkan kolom status transfer di penetasan
        Schema::table('penetasan', function (Blueprint $table) {
            if (!Schema::hasColumn('penetasan', 'doc_ditransfer')) {
                $table->integer('doc_ditransfer')->default(0)->comment('Jumlah DOQ yang sudah ditransfer ke pembesaran');
            }
            
            if (!Schema::hasColumn('penetasan', 'telur_infertil_ditransfer')) {
                $table->integer('telur_infertil_ditransfer')->default(0)->comment('Jumlah telur infertil yang ditransfer ke produksi');
            }
        });

        // Tambahkan kolom status transfer di pembesaran
        Schema::table('pembesaran', function (Blueprint $table) {
            if (!Schema::hasColumn('pembesaran', 'indukan_ditransfer')) {
                $table->integer('indukan_ditransfer')->default(0)->comment('Jumlah indukan yang sudah ditransfer ke produksi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksi', function (Blueprint $table) {
            $table->dropForeign(['penetasan_id']);
            $table->dropForeign(['pembesaran_id']);
            $table->dropColumn([
                'penetasan_id',
                'pembesaran_id',
                'jumlah_indukan',
                'umur_mulai_produksi',
                'tanggal_mulai',
                'tanggal_akhir',
                'status'
            ]);
        });

        Schema::table('pembesaran', function (Blueprint $table) {
            if (Schema::hasColumn('pembesaran', 'penetasan_id')) {
                $table->dropForeign(['penetasan_id']);
                $table->dropColumn('penetasan_id');
            }
            $table->dropColumn('indukan_ditransfer');
        });

        Schema::table('penetasan', function (Blueprint $table) {
            $table->dropColumn(['doc_ditransfer', 'telur_infertil_ditransfer']);
        });
    }
};
