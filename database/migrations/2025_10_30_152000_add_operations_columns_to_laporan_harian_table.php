<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_harian', 'sisa_pakan_kg')) {
                $table->decimal('sisa_pakan_kg', 10, 2)->nullable()->after('konsumsi_pakan_kg')->comment('Sisa pakan harian (kg)');
            }
            if (!Schema::hasColumn('laporan_harian', 'sisa_tray_bal')) {
                $table->decimal('sisa_tray_bal', 8, 2)->nullable()->after('sisa_pakan_kg')->comment('Sisa tray dalam bal (1 bal = 100 lembar)');
            }
            if (!Schema::hasColumn('laporan_harian', 'sisa_tray_lembar')) {
                $table->integer('sisa_tray_lembar')->nullable()->after('sisa_tray_bal')->comment('Sisa tray dalam lembar');
            }
            if (!Schema::hasColumn('laporan_harian', 'sisa_vitamin_liter')) {
                $table->decimal('sisa_vitamin_liter', 8, 2)->nullable()->after('sisa_tray_lembar')->comment('Sisa vitamin cair (liter)');
            }
            if (!Schema::hasColumn('laporan_harian', 'sisa_telur')) {
                $table->integer('sisa_telur')->nullable()->after('sisa_vitamin_liter')->comment('Sisa telur hari ini (butir)');
            }
            if (!Schema::hasColumn('laporan_harian', 'penjualan_telur_butir')) {
                $table->integer('penjualan_telur_butir')->nullable()->after('sisa_telur')->comment('Jumlah telur yang terjual (butir)');
            }
            if (!Schema::hasColumn('laporan_harian', 'penjualan_puyuh_ekor')) {
                $table->integer('penjualan_puyuh_ekor')->nullable()->after('penjualan_telur_butir')->comment('Jumlah burung puyuh yang terjual (ekor)');
            }
            if (!Schema::hasColumn('laporan_harian', 'pendapatan_harian')) {
                $table->decimal('pendapatan_harian', 12, 2)->nullable()->after('penjualan_puyuh_ekor')->comment('Total pendapatan harian (Rp)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $columns = [
                'pendapatan_harian',
                'penjualan_puyuh_ekor',
                'penjualan_telur_butir',
                'sisa_telur',
                'sisa_vitamin_liter',
                'sisa_tray_lembar',
                'sisa_tray_bal',
                'sisa_pakan_kg',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('laporan_harian', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
