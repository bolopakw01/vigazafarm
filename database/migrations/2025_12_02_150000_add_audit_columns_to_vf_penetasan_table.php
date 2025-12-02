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
            if (!Schema::hasColumn('vf_penetasan', 'created_by')) {
                $table->unsignedBigInteger('created_by')
                    ->nullable()
                    ->after('fase_penetasan');

                $table->foreign('created_by')
                    ->references('id')
                    ->on('vf_pengguna')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('vf_penetasan', 'updated_by')) {
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
        Schema::table('vf_penetasan', function (Blueprint $table) {
            if (Schema::hasColumn('vf_penetasan', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }

            if (Schema::hasColumn('vf_penetasan', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
