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
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->enum('jenis_kelamin_kematian', ['jantan', 'betina', 'campuran'])->nullable()->after('jumlah_kematian');
            $table->text('keterangan_kematian')->nullable()->after('jenis_kelamin_kematian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin_kematian', 'keterangan_kematian']);
        });
    }
};
