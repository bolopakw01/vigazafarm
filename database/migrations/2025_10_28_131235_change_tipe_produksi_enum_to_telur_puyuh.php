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
        // First, modify enum to include both old and new values
        DB::statement("ALTER TABLE produksi MODIFY COLUMN tipe_produksi ENUM('layer', 'broiler', 'telur', 'puyuh') DEFAULT 'telur'");

        // Then update existing values
        DB::table('produksi')->where('tipe_produksi', 'layer')->update(['tipe_produksi' => 'telur']);
        DB::table('produksi')->where('tipe_produksi', 'broiler')->update(['tipe_produksi' => 'puyuh']);

        // Finally, modify enum to only include new values
        DB::statement("ALTER TABLE produksi MODIFY COLUMN tipe_produksi ENUM('telur', 'puyuh') DEFAULT 'telur'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, modify enum to include both old and new values
        DB::statement("ALTER TABLE produksi MODIFY COLUMN tipe_produksi ENUM('layer', 'broiler', 'telur', 'puyuh') DEFAULT 'layer'");

        // Then revert values
        DB::table('produksi')->where('tipe_produksi', 'telur')->update(['tipe_produksi' => 'layer']);
        DB::table('produksi')->where('tipe_produksi', 'puyuh')->update(['tipe_produksi' => 'broiler']);

        // Finally, modify enum to only include old values
        DB::statement("ALTER TABLE produksi MODIFY COLUMN tipe_produksi ENUM('layer', 'broiler') DEFAULT 'layer'");
    }
};
