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
        $tableName = $this->resolvePenetasanTable();

        if (!$tableName) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'tanggal_masuk_hatcher')) {
                $table->date('tanggal_masuk_hatcher')->nullable()->after('estimasi_tanggal_menetas');
            }

            if (!Schema::hasColumn($tableName, 'fase_penetasan')) {
                $table->enum('fase_penetasan', ['setter', 'hatcher'])->default('setter')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = $this->resolvePenetasanTable();

        if (!$tableName) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (Schema::hasColumn($tableName, 'fase_penetasan')) {
                $table->dropColumn('fase_penetasan');
            }

            if (Schema::hasColumn($tableName, 'tanggal_masuk_hatcher')) {
                $table->dropColumn('tanggal_masuk_hatcher');
            }
        });
    }

    private function resolvePenetasanTable(): ?string
    {
        if (Schema::hasTable('vf_penetasan')) {
            return 'vf_penetasan';
        }

        if (Schema::hasTable('penetasan')) {
            return 'penetasan';
        }

        return null;
    }
};
