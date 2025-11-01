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
        if (!Schema::hasColumn('produksi', 'berat_rata_rata')) {
            Schema::table('produksi', function (Blueprint $table) {
                $table->decimal('berat_rata_rata', 8, 2)->nullable()->after('jumlah_betina');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('produksi', 'berat_rata_rata')) {
            Schema::table('produksi', function (Blueprint $table) {
                $table->dropColumn('berat_rata_rata');
            });
        }
    }
};
