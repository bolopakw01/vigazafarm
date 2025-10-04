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
            $table->foreignId('penetasan_id')->nullable()->after('batch_produksi_id')->constrained('penetasan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembesaran', function (Blueprint $table) {
            $table->dropForeign(['penetasan_id']);
            $table->dropColumn('penetasan_id');
        });
    }
};
