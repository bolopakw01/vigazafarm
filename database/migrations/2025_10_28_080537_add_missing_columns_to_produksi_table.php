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
            // Drop foreign key constraint temporarily
            $table->dropForeign(['batch_produksi_id']);

            // Change batch_produksi_id to string
            $table->string('batch_produksi_id', 50)->change();

            // Add missing columns
            if (!Schema::hasColumn('produksi', 'jumlah_telur')) {
                $table->integer('jumlah_telur')->nullable()->after('jumlah_indukan');
            }

            if (!Schema::hasColumn('produksi', 'berat_rata_telur')) {
                $table->decimal('berat_rata_telur', 8, 2)->nullable()->after('jumlah_telur');
            }

            if (!Schema::hasColumn('produksi', 'harga_per_kg')) {
                $table->decimal('harga_per_kg', 10, 2)->nullable()->after('berat_rata_telur');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksi', function (Blueprint $table) {
            $table->dropColumn(['jumlah_telur', 'berat_rata_telur', 'harga_per_kg']);
            // Note: Cannot easily revert string back to integer without data loss
            // Foreign key will need to be manually recreated if needed
        });
    }
};
