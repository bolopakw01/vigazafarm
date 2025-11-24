<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi', function (Blueprint $table) {
            if (!Schema::hasColumn('produksi', 'produksi_sumber_id')) {
                $table->unsignedBigInteger('produksi_sumber_id')
                    ->nullable()
                    ->after('pembesaran_id');
                $table->foreign('produksi_sumber_id')
                    ->references('id')
                    ->on('produksi')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('produksi', function (Blueprint $table) {
            if (Schema::hasColumn('produksi', 'produksi_sumber_id')) {
                $table->dropForeign(['produksi_sumber_id']);
                $table->dropColumn('produksi_sumber_id');
            }
        });
    }
};
