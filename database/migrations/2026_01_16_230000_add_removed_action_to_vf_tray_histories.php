<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vf_tray_histories')) {
            return;
        }

        DB::statement("ALTER TABLE vf_tray_histories MODIFY COLUMN action ENUM('created','updated','deleted','removed') NOT NULL");
    }

    public function down(): void
    {
        if (!Schema::hasTable('vf_tray_histories')) {
            return;
        }

        DB::statement("ALTER TABLE vf_tray_histories MODIFY COLUMN action ENUM('created','updated','deleted') NOT NULL");
    }
};
