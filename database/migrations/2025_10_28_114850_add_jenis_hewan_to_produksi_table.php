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
            $table->enum('jenis_hewan', ['burung', 'puyuh'])
                  ->default('burung')
                  ->after('batch_produksi_id')
                  ->comment('Jenis hewan: burung atau puyuh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksi', function (Blueprint $table) {
            $table->dropColumn('jenis_hewan');
        });
    }
};
