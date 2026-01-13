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
        Schema::table('vf_laporan_harian', function (Blueprint $table) {
            $table->enum('jenis_kelamin_penjualan', ['jantan', 'betina', 'campuran'])->nullable()->after('nama_tray_penjualan');
            $table->integer('penjualan_puyuh_jantan')->default(0)->after('jenis_kelamin_penjualan');
            $table->integer('penjualan_puyuh_betina')->default(0)->after('penjualan_puyuh_jantan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vf_laporan_harian', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin_penjualan', 'penjualan_puyuh_jantan', 'penjualan_puyuh_betina']);
        });
    }
};
