<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_harian', 'harga_pakan_per_kg')) {
                $table->decimal('harga_pakan_per_kg', 12, 2)->nullable()->after('sisa_pakan_kg')->comment('Harga pakan per kg');
            }

            if (!Schema::hasColumn('laporan_harian', 'biaya_pakan_harian')) {
                $table->decimal('biaya_pakan_harian', 14, 2)->nullable()->after('harga_pakan_per_kg')->comment('Total biaya pakan per hari');
            }

            if (!Schema::hasColumn('laporan_harian', 'harga_vitamin_per_liter')) {
                $table->decimal('harga_vitamin_per_liter', 12, 2)->nullable()->after('sisa_vitamin_liter')->comment('Harga vitamin per liter');
            }

            if (!Schema::hasColumn('laporan_harian', 'biaya_vitamin_harian')) {
                $table->decimal('biaya_vitamin_harian', 14, 2)->nullable()->after('harga_vitamin_per_liter')->comment('Total biaya vitamin per hari');
            }
        });
    }

    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $columns = [
                'biaya_vitamin_harian',
                'harga_vitamin_per_liter',
                'biaya_pakan_harian',
                'harga_pakan_per_kg',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('laporan_harian', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
