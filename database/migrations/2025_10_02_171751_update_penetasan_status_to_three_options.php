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
        Schema::table('penetasan', function (Blueprint $table) {
            // Update existing 'aktif' status to 'proses'
            DB::table('penetasan')->where('status', 'aktif')->update(['status' => 'proses']);
            
            // Drop old status column
            $table->dropColumn('status');
        });
        
        Schema::table('penetasan', function (Blueprint $table) {
            // Add new status column with only 3 options
            $table->enum('status', ['proses', 'selesai', 'gagal'])->default('proses')->after('catatan');
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
        
        Schema::table('penetasan', function (Blueprint $table) {
            // Restore old status with 4 options
            $table->enum('status', ['proses', 'aktif', 'selesai', 'gagal'])->default('proses')->after('catatan');
        });
    }
};
