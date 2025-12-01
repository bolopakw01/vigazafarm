<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = $this->resolvePenggunaTable();

        if (!$tableName) {
            return;
        }

        if (!Schema::hasColumn($tableName, 'nomor_telepon')) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'surel')) {
                    $table->string('nomor_telepon', 30)->nullable()->after('surel');
                } else {
                    $table->string('nomor_telepon', 30)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        $tableName = $this->resolvePenggunaTable();

        if (!$tableName) {
            return;
        }

        if (Schema::hasColumn($tableName, 'nomor_telepon')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('nomor_telepon');
            });
        }
    }

    private function resolvePenggunaTable(): ?string
    {
        if (Schema::hasTable('vf_pengguna')) {
            return 'vf_pengguna';
        }

        if (Schema::hasTable('pengguna')) {
            return 'pengguna';
        }

        return null;
    }
};
