<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $pakanTable = $this->resolveTable(['vf_pakan', 'pakan']);
        $historyTable = $this->resolveTable(['vf_feed_histories', 'feed_histories']);

        if ($pakanTable && !Schema::hasColumn($pakanTable, 'sisa_pakan_kg')) {
            Schema::table($pakanTable, function (Blueprint $table) {
                $table->decimal('sisa_pakan_kg', 10, 2)->nullable()->after('jumlah_karung');
            });
        }

        if ($historyTable && !Schema::hasColumn($historyTable, 'sisa_pakan_kg')) {
            Schema::table($historyTable, function (Blueprint $table) use ($historyTable) {
                $column = $table->decimal('sisa_pakan_kg', 10, 2)->nullable();

                if (Schema::hasColumn($historyTable, 'jumlah_karung_sisa')) {
                    $column->after('jumlah_karung_sisa');
                }
            });
        }
    }

    public function down(): void
    {
        $pakanTable = $this->resolveTable(['vf_pakan', 'pakan']);
        $historyTable = $this->resolveTable(['vf_feed_histories', 'feed_histories']);

        if ($historyTable && Schema::hasColumn($historyTable, 'sisa_pakan_kg')) {
            Schema::table($historyTable, function (Blueprint $table) {
                $table->dropColumn('sisa_pakan_kg');
            });
        }

        if ($pakanTable && Schema::hasColumn($pakanTable, 'sisa_pakan_kg')) {
            Schema::table($pakanTable, function (Blueprint $table) {
                $table->dropColumn('sisa_pakan_kg');
            });
        }
    }

    private function resolveTable(array $candidates): ?string
    {
        foreach ($candidates as $name) {
            if (Schema::hasTable($name)) {
                return $name;
            }
        }

        return null;
    }
};
