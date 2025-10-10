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
        // Update biaya column in kesehatan table to support larger values
        Schema::table('kesehatan', function (Blueprint $table) {
            $table->decimal('biaya', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kesehatan', function (Blueprint $table) {
            $table->decimal('biaya', 10, 2)->nullable()->change();
        });
    }
};
