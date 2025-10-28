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
            if (!Schema::hasColumn('produksi', 'harga_per_pcs')) {
                $table->decimal('harga_per_pcs', 10, 2)->nullable()->after('harga_per_kg')->comment('Harga per pcs/butir telur');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksi', function (Blueprint $table) {
            if (Schema::hasColumn('produksi', 'harga_per_pcs')) {
                $table->dropColumn('harga_per_pcs');
            }
        });
    }
};
