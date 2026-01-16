<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vf_laporan_harian', function (Blueprint $table) {
            if (!Schema::hasColumn('vf_laporan_harian', 'jumlah_kematian_jantan')) {
                $table->integer('jumlah_kematian_jantan')->nullable()->after('jumlah_kematian');
            }
            if (!Schema::hasColumn('vf_laporan_harian', 'jumlah_kematian_betina')) {
                $table->integer('jumlah_kematian_betina')->nullable()->after('jumlah_kematian_jantan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vf_laporan_harian', function (Blueprint $table) {
            if (Schema::hasColumn('vf_laporan_harian', 'jumlah_kematian_betina')) {
                $table->dropColumn('jumlah_kematian_betina');
            }
            if (Schema::hasColumn('vf_laporan_harian', 'jumlah_kematian_jantan')) {
                $table->dropColumn('jumlah_kematian_jantan');
            }
        });
    }
};
