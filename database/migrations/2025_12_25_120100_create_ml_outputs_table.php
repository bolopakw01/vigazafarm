<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vf_ml_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('run_id')->nullable()->constrained('vf_ml_runs')->nullOnDelete();
            $table->string('type', 40); // egg_forecast, feed_need, mortality_anomaly, price_rec, summary
            $table->string('entity_type', 40)->nullable(); // produksi|batch
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->date('tanggal_prediksi')->nullable();
            $table->unsignedSmallInteger('horizon')->default(0);
            $table->decimal('nilai', 15, 2)->nullable();
            $table->decimal('lower', 15, 2)->nullable();
            $table->decimal('upper', 15, 2)->nullable();
            $table->decimal('score', 10, 4)->nullable(); // z-score / confidence
            $table->string('status_flag', 20)->default('normal');
            $table->json('top_features')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['type', 'tanggal_prediksi']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vf_ml_outputs');
    }
};
