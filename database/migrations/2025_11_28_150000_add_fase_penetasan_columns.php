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
            if (!Schema::hasColumn('vf_penetasan', 'tanggal_masuk_hatcher')) {
                $table->date('tanggal_masuk_hatcher')->nullable()->after('estimasi_tanggal_menetas');
            }

            if (!Schema::hasColumn('vf_penetasan', 'fase_penetasan')) {
                $table->enum('fase_penetasan', ['setter', 'hatcher'])->default('setter')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vf_penetasan', function (Blueprint $table) {
            if (Schema::hasColumn('vf_penetasan', 'fase_penetasan')) {
                $table->dropColumn('fase_penetasan');
            }

            if (Schema::hasColumn('vf_penetasan', 'tanggal_masuk_hatcher')) {
                $table->dropColumn('tanggal_masuk_hatcher');
            }
        });
    }
};
