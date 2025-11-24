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
        Schema::table('produksi', function (Blueprint $table) {
            if (!Schema::hasColumn('produksi', 'persentase_fertil')) {
                $table->decimal('persentase_fertil', 5, 2)->nullable()->after('berat_rata_telur')->comment('Persentase fertil telur (%)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksi', function (Blueprint $table) {
            if (Schema::hasColumn('produksi', 'persentase_fertil')) {
                $table->dropColumn('persentase_fertil');
            }
        });
    }
};
