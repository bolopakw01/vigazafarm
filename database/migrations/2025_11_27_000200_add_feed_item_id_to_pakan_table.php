<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pakan') || !Schema::hasTable('feed_vitamin_items')) {
            return;
        }

        if (Schema::hasColumn('pakan', 'feed_item_id')) {
            return;
        }

        Schema::table('pakan', function (Blueprint $table) {
            $table->foreignId('feed_item_id')
                ->nullable()
                ->after('stok_pakan_id')
                ->constrained('feed_vitamin_items')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('pakan')) {
            return;
        }

        if (!Schema::hasColumn('pakan', 'feed_item_id')) {
            return;
        }

        Schema::table('pakan', function (Blueprint $table) {
            $table->dropForeign(['feed_item_id']);
            $table->dropColumn('feed_item_id');
        });
    }
};
