<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vf_kesehatan', function (Blueprint $table) {
            if (!Schema::hasColumn('vf_kesehatan', 'kandang_tujuan_id')) {
                $table->foreignId('kandang_tujuan_id')
                    ->nullable()
                    ->after('jumlah_burung')
                    ->constrained('vf_kandang')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('vf_kesehatan', 'feed_vitamin_item_id')) {
                $table->foreignId('feed_vitamin_item_id')
                    ->nullable()
                    ->after('nama_vaksin_obat')
                    ->constrained('vf_feed_vitamin_items')
                    ->nullOnDelete();
            }
        });

        if (Schema::hasColumn('vf_kesehatan', 'tipe_kegiatan')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE vf_kesehatan MODIFY tipe_kegiatan ENUM('vaksinasi','pengobatan','pemeriksaan_rutin','karantina','vitamin') NOT NULL");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vf_kesehatan', function (Blueprint $table) {
            if (Schema::hasColumn('vf_kesehatan', 'feed_vitamin_item_id')) {
                $table->dropForeign(['feed_vitamin_item_id']);
                $table->dropColumn('feed_vitamin_item_id');
            }

            if (Schema::hasColumn('vf_kesehatan', 'kandang_tujuan_id')) {
                $table->dropForeign(['kandang_tujuan_id']);
                $table->dropColumn('kandang_tujuan_id');
            }
        });

        if (Schema::hasColumn('vf_kesehatan', 'tipe_kegiatan')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE vf_kesehatan MODIFY tipe_kegiatan ENUM('vaksinasi','pengobatan','pemeriksaan_rutin','karantina') NOT NULL");
            }
        }
    }
};
