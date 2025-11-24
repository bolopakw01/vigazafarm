<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE produksi MODIFY jenis_input ENUM('manual','dari_pembesaran','dari_penetasan','dari_produksi') NOT NULL DEFAULT 'manual'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE produksi MODIFY jenis_input ENUM('manual','dari_pembesaran','dari_penetasan') NOT NULL DEFAULT 'manual'");
    }
};
