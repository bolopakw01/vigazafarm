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
            if (!Schema::hasColumn('laporan_harian', 'vitamin_terpakai')) {
                $table->decimal('vitamin_terpakai', 8, 3)->nullable()->after('sisa_vitamin_liter')->comment('Vitamin terpakai harian (liter)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_harian', 'vitamin_terpakai')) {
                $table->dropColumn('vitamin_terpakai');
            }
        });
    }
};
