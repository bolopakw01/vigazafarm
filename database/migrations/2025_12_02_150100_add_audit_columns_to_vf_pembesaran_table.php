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
        Schema::table('vf_pembesaran', function (Blueprint $table) {
            if (!Schema::hasColumn('vf_pembesaran', 'created_by')) {
                $table->unsignedBigInteger('created_by')
                    ->nullable()
                    ->after('status_batch');

                $table->foreign('created_by')
                    ->references('id')
                    ->on('vf_pengguna')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('vf_pembesaran', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')
                    ->nullable()
                    ->after('created_by');

                $table->foreign('updated_by')
                    ->references('id')
                    ->on('vf_pengguna')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vf_pembesaran', function (Blueprint $table) {
            if (Schema::hasColumn('vf_pembesaran', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }

            if (Schema::hasColumn('vf_pembesaran', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
