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
        Schema::create('berat_sampling', function (Blueprint $table) {
            $table->id();
            $table->string('batch_produksi_id', 50);
            $table->date('tanggal_sampling');
            $table->integer('umur_hari');
            $table->decimal('berat_rata_rata', 10, 2); // gram
            $table->integer('jumlah_sampel')->nullable(); // berapa ekor yang ditimbang
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
            
            // Note: batch_produksi.id is string, so no foreign key constraint for now
            // Or we can add it manually if needed
            $table->index('tanggal_sampling');
            $table->index('batch_produksi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berat_sampling');
    }
};
