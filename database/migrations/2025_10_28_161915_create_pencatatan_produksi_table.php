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
        Schema::create('pencatatan_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_id')->constrained('produksi')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('jumlah_produksi');
            $table->enum('kualitas', ['baik', 'sedang', 'buruk'])->default('baik');
            $table->decimal('berat_rata_rata', 8, 2)->nullable();
            $table->decimal('harga_per_unit', 10, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('pengguna')->onDelete('cascade');
            $table->timestamps();

            // Index untuk performa
            $table->index(['produksi_id', 'tanggal']);
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencatatan_produksi');
    }
};
