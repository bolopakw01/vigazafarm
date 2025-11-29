<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('vf_pakan', 'sisa_pakan_kg')) {
            Schema::table('vf_pakan', function (Blueprint $table) {
                $table->decimal('sisa_pakan_kg', 10, 2)->nullable()->after('jumlah_karung');
            });
        }

        if (!Schema::hasColumn('vf_feed_histories', 'sisa_pakan_kg')) {
            Schema::table('vf_feed_histories', function (Blueprint $table) {
                $table->decimal('sisa_pakan_kg', 10, 2)->nullable()->after('jumlah_karung_sisa');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vf_feed_histories', 'sisa_pakan_kg')) {
            Schema::table('vf_feed_histories', function (Blueprint $table) {
                $table->dropColumn('sisa_pakan_kg');
            });
        }

        if (Schema::hasColumn('vf_pakan', 'sisa_pakan_kg')) {
            Schema::table('vf_pakan', function (Blueprint $table) {
                $table->dropColumn('sisa_pakan_kg');
            });
        }
    }
};
