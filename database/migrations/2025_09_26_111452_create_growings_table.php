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
        Schema::create('growings', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            $table->integer('chick_count');
            $table->enum('gender', ['betina', 'jantan'])->nullable();
            $table->date('ready_date')->nullable(); // Computed: entry_date + 28-35 days
            $table->integer('ready_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('growings');
    }
};
