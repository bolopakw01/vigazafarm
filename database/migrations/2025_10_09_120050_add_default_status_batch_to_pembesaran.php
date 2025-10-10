<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update semua data yang NULL menjadi 'Aktif'
        DB::table('pembesaran')
            ->whereNull('status_batch')
            ->update(['status_batch' => 'Aktif']);
            
        // Tambahkan default value untuk kolom status_batch
        Schema::table('pembesaran', function (Blueprint $table) {
            $table->string('status_batch')->default('Aktif')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembesaran', function (Blueprint $table) {
            $table->string('status_batch')->nullable()->change();
        });
    }
};
