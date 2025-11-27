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
        Schema::table('vf_penetasan', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('vf_pengguna')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('vf_pengguna')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vf_penetasan', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['updated_by']);
        });
    }
};
