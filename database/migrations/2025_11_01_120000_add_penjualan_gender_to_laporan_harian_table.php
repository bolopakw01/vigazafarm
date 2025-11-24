<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_harian', 'jenis_kelamin_penjualan')) {
                $table->enum('jenis_kelamin_penjualan', ['jantan', 'betina'])
                    ->nullable()
                    ->after('penjualan_puyuh_ekor')
                    ->comment('Jenis kelamin puyuh yang dijual');
            }
        });
    }

    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_harian', 'jenis_kelamin_penjualan')) {
                $table->dropColumn('jenis_kelamin_penjualan');
            }
        });
    }
};
