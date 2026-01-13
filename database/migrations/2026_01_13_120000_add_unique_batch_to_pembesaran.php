<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vf_pembesaran', function (Blueprint $table) {
            // Enforce one pembesaran per batch_produksi_id
            $table->unique('batch_produksi_id', 'vf_pembesaran_batch_produksi_unique');
        });
    }

    public function down(): void
    {
        Schema::table('vf_pembesaran', function (Blueprint $table) {
            $table->dropUnique('vf_pembesaran_batch_produksi_unique');
        });
    }
};
