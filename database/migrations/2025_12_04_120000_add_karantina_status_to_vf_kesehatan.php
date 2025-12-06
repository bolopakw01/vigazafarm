<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vf_kesehatan', function (Blueprint $table) {
            $table->boolean('karantina_dikembalikan')->default(false)->after('jumlah_burung');
            $table->timestamp('karantina_dikembalikan_pada')->nullable()->after('karantina_dikembalikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vf_kesehatan', function (Blueprint $table) {
            $table->dropColumn(['karantina_dikembalikan', 'karantina_dikembalikan_pada']);
        });
    }
};
