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
        Schema::table('pakan', function (Blueprint $table) {
            // Drop foreign key jika ada
            $table->dropForeign(['batch_produksi_id']);
            
            // Ubah tipe kolom dari bigint ke varchar
            $table->string('batch_produksi_id', 50)->nullable()->change();
            
            // Re-add foreign key constraint (optional, jika tabel batch_produksi ada)
            // $table->foreign('batch_produksi_id')->references('id')->on('batch_produksi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pakan', function (Blueprint $table) {
            // Kembalikan ke bigint jika rollback
            $table->dropForeign(['batch_produksi_id']);
            $table->unsignedBigInteger('batch_produksi_id')->nullable()->change();
        });
    }
};
