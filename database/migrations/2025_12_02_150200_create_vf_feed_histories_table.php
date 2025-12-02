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
        Schema::create('vf_feed_histories', function (Blueprint $table) {
            $table->id();
            $table->string('batch_produksi_id')->nullable();
            $table->unsignedBigInteger('stok_pakan_id')->nullable();
            $table->unsignedBigInteger('feed_item_id')->nullable();
            $table->date('tanggal')->nullable();
            $table->integer('jumlah_karung_sisa')->default(0);
            $table->decimal('sisa_pakan_kg', 10, 2)->default(0);
            $table->string('keterangan', 255)->nullable();
            $table->unsignedBigInteger('pengguna_id')->nullable();
            $table->timestamps();

            $table->index('batch_produksi_id');
            $table->index('tanggal');

            $table->foreign('stok_pakan_id')
                ->references('id')
                ->on('vf_stok_pakan')
                ->nullOnDelete();

            $table->foreign('feed_item_id')
                ->references('id')
                ->on('vf_feed_vitamin_items')
                ->nullOnDelete();

            $table->foreign('pengguna_id')
                ->references('id')
                ->on('vf_pengguna')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vf_feed_histories');
    }
};
