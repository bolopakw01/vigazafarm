<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Tambahkan opsi 'penuh' pada enum status kandang
        DB::statement("ALTER TABLE `vf_kandang` MODIFY `status` ENUM('aktif','maintenance','kosong','penuh') NOT NULL DEFAULT 'aktif'");
    }

    public function down(): void
    {
        // Kembalikan ke enum awal tanpa 'penuh'
        DB::statement("ALTER TABLE `vf_kandang` MODIFY `status` ENUM('aktif','maintenance','kosong') NOT NULL DEFAULT 'aktif'");
    }
};
