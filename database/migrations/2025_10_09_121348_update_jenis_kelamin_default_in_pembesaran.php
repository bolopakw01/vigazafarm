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
        // Ubah tipe kolom dari enum ke string terlebih dahulu
        DB::statement("ALTER TABLE pembesaran MODIFY COLUMN jenis_kelamin VARCHAR(20) DEFAULT 'campuran'");
        
        // Update semua data yang NULL atau kosong menjadi 'campuran'
        DB::table('pembesaran')
            ->where(function($query) {
                $query->whereNull('jenis_kelamin')
                      ->orWhere('jenis_kelamin', '');
            })
            ->update(['jenis_kelamin' => 'campuran']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert campuran back to NULL
        DB::table('pembesaran')
            ->where('jenis_kelamin', 'campuran')
            ->update(['jenis_kelamin' => null]);
            
        // Revert back to enum
        DB::statement("ALTER TABLE pembesaran MODIFY COLUMN jenis_kelamin ENUM('betina', 'jantan') NULL");
    }
};
