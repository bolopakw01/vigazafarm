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
        Schema::table('produksi', function (Blueprint $table) {
            $table->enum('jenis_input', ['manual', 'dari_pembesaran', 'dari_penetasan'])
                  ->default('manual')
                  ->after('batch_produksi_id')
                  ->comment('Sumber input produksi: manual, dari pembesaran, atau dari penetasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksi', function (Blueprint $table) {
            $table->dropColumn('jenis_input');
        });
    }
};
