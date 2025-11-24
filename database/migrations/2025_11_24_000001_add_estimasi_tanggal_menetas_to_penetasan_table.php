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
        Schema::table('penetasan', function (Blueprint $table) {
            $table->date('estimasi_tanggal_menetas')->nullable()->after('tanggal_simpan_telur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penetasan', function (Blueprint $table) {
            $table->dropColumn('estimasi_tanggal_menetas');
        });
    }
};
