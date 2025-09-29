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
        Schema::create('hatchings', function (Blueprint $table) {
            $table->id();
            $table->date('egg_storage_date');
            $table->integer('egg_count');
            $table->date('hatching_date')->nullable();
            $table->integer('hatched_count')->nullable();
            $table->integer('doc_count')->nullable(); // Day Old Chick
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hatchings');
    }
};
