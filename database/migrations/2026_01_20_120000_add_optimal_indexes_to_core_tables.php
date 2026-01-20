<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vf_kandang')) {
            Schema::table('vf_kandang', function (Blueprint $table) {
                $table->index('tipe_kandang', 'vf_kandang_tipe_kandang_index');
                $table->index('status', 'vf_kandang_status_index');
            });
        }

        if (Schema::hasTable('vf_stok_pakan')) {
            Schema::table('vf_stok_pakan', function (Blueprint $table) {
                $table->index('jenis_pakan', 'vf_stok_pakan_jenis_pakan_index');
                $table->index('tanggal_kadaluarsa', 'vf_stok_pakan_tanggal_kadaluarsa_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vf_kandang')) {
            Schema::table('vf_kandang', function (Blueprint $table) {
                $table->dropIndex('vf_kandang_tipe_kandang_index');
                $table->dropIndex('vf_kandang_status_index');
            });
        }

        if (Schema::hasTable('vf_stok_pakan')) {
            Schema::table('vf_stok_pakan', function (Blueprint $table) {
                $table->dropIndex('vf_stok_pakan_jenis_pakan_index');
                $table->dropIndex('vf_stok_pakan_tanggal_kadaluarsa_index');
            });
        }
    }
};

