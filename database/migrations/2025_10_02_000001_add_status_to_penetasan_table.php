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
            // Status: proses (baru input), aktif (sudah 1 hari), selesai (sudah menetas), gagal (owner set gagal)
            $table->enum('status', ['proses', 'aktif', 'selesai', 'gagal'])->default('proses')->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penetasan', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
