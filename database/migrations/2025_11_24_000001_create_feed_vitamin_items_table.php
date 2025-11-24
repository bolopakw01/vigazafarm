<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_vitamin_items', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['pakan', 'vitamin'])->index();
            $table->string('name');
            $table->decimal('price', 14, 2)->default(0);
            $table->string('unit', 50)->default('kg');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_vitamin_items');
    }
};
