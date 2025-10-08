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
        // Rename timestamp columns to Indonesian for tables that were missed
        // in the original rename migration
        
        // laporan_harian
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->renameColumn('created_at', 'dibuat_pada');
            $table->renameColumn('updated_at', 'diperbarui_pada');
        });
        
        // monitoring_lingkungan (if not already renamed)
        if (Schema::hasColumn('monitoring_lingkungan', 'created_at')) {
            Schema::table('monitoring_lingkungan', function (Blueprint $table) {
                $table->renameColumn('created_at', 'dibuat_pada');
                $table->renameColumn('updated_at', 'diperbarui_pada');
            });
        }
        
        // kesehatan (if not already renamed)
        if (Schema::hasColumn('kesehatan', 'created_at')) {
            Schema::table('kesehatan', function (Blueprint $table) {
                $table->renameColumn('created_at', 'dibuat_pada');
                $table->renameColumn('updated_at', 'diperbarui_pada');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to English
        
        // laporan_harian
        if (Schema::hasColumn('laporan_harian', 'dibuat_pada')) {
            Schema::table('laporan_harian', function (Blueprint $table) {
                $table->renameColumn('dibuat_pada', 'created_at');
                $table->renameColumn('diperbarui_pada', 'updated_at');
            });
        }
        
        // monitoring_lingkungan
        if (Schema::hasColumn('monitoring_lingkungan', 'dibuat_pada')) {
            Schema::table('monitoring_lingkungan', function (Blueprint $table) {
                $table->renameColumn('dibuat_pada', 'created_at');
                $table->renameColumn('diperbarui_pada', 'updated_at');
            });
        }
        
        // kesehatan
        if (Schema::hasColumn('kesehatan', 'dibuat_pada')) {
            Schema::table('kesehatan', function (Blueprint $table) {
                $table->renameColumn('dibuat_pada', 'created_at');
                $table->renameColumn('diperbarui_pada', 'updated_at');
            });
        }
    }
};
