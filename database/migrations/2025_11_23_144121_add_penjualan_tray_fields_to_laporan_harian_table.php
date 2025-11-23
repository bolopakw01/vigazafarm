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
        Schema::table('laporan_harian', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_harian', 'tray_penjualan_id')) {
                $table->unsignedBigInteger('tray_penjualan_id')->nullable()->after('pendapatan_harian')->comment('ID tray yang dijual (foreign key ke laporan_harian.id)');
            }
            if (!Schema::hasColumn('laporan_harian', 'harga_per_butir')) {
                $table->decimal('harga_per_butir', 8, 2)->nullable()->after('tray_penjualan_id')->comment('Harga jual per butir telur (Rp)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $columns = [
                'harga_per_butir',
                'tray_penjualan_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('laporan_harian', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
