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
        Schema::table('pembesaran', function (Blueprint $table) {
            $table->decimal('target_berat_akhir', 8, 2)->nullable()->after('berat_rata_rata');
            $table->string('kondisi_doc')->nullable()->after('target_berat_akhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembesaran', function (Blueprint $table) {
            $table->dropColumn(['target_berat_akhir', 'kondisi_doc']);
        });
    }
};
