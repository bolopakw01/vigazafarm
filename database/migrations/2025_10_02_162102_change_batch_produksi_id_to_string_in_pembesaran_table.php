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
        Schema::table('pembesaran', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['batch_produksi_id']);
            
            // Change column type to string
            $table->string('batch_produksi_id', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembesaran', function (Blueprint $table) {
            // Revert back to foreignId (integer)
            $table->foreignId('batch_produksi_id')->nullable()->change();
            $table->foreign('batch_produksi_id')->references('id')->on('batch_produksi')->onDelete('set null');
        });
    }
};
