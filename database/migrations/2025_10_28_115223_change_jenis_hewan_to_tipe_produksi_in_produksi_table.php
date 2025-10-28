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
            $table->dropColumn('jenis_hewan');
            $table->enum('tipe_produksi', ['layer', 'broiler'])
                  ->default('layer')
                  ->after('batch_produksi_id')
                  ->comment('Tipe produksi: telur (layer) atau puyuh (broiler)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksi', function (Blueprint $table) {
            $table->dropColumn('tipe_produksi');
            $table->enum('jenis_hewan', ['burung', 'puyuh'])
                  ->default('burung')
                  ->after('batch_produksi_id')
                  ->comment('Jenis hewan: burung atau puyuh');
        });
    }
};
