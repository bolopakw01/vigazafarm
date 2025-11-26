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
        if (!Schema::hasColumn('kesehatan', 'catatan')) {
            Schema::table('kesehatan', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('jumlah_burung');
            });
        }

        foreach (['gejala', 'diagnosa', 'tindakan'] as $column) {
            if (Schema::hasColumn('kesehatan', $column)) {
                Schema::table('kesehatan', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('kesehatan', 'catatan')) {
            Schema::table('kesehatan', function (Blueprint $table) {
                $table->dropColumn('catatan');
            });
        }

        foreach (['gejala', 'diagnosa', 'tindakan'] as $column) {
            if (!Schema::hasColumn('kesehatan', $column)) {
                Schema::table('kesehatan', function (Blueprint $table) use ($column) {
                    $table->text($column)->nullable()->after('jumlah_burung');
                });
            }
        }
    }
};
