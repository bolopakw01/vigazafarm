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
        Schema::table('kematian', function (Blueprint $table) {
            // Make produksi_id nullable because pembesaran phase doesn't have produksi_id yet
            $table->unsignedBigInteger('produksi_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kematian', function (Blueprint $table) {
            // Revert back to NOT NULL (but this will fail if there are null values)
            $table->unsignedBigInteger('produksi_id')->nullable(false)->change();
        });
    }
};
