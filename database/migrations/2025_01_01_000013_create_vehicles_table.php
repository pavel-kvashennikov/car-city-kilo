<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('slug')->unique();
            $table->foreignId('dealer_profile_id')->constrained('dealer_profiles')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('car_brands');
            $table->foreignId('model_id')->constrained('car_models');
            $table->foreignId('generation_id')->nullable()->constrained('car_generations');
            $table->smallInteger('year');
            $table->string('vin', 17)->nullable();
            $table->integer('mileage')->default(0);
            $table->string('color', 50)->nullable();
            $table->string('engine_type', 20)->nullable();
            $table->decimal('engine_volume', 3, 1)->nullable();
            $table->smallInteger('engine_power')->nullable();
            $table->string('transmission', 20)->nullable();
            $table->string('drive_type', 10)->nullable(); // fwd, rwd, awd
            $table->string('body_type', 30)->nullable();
            $table->string('condition', 20)->default('used');
            $table->string('legal_status', 20)->default('clean'); // clean, credit, seized
            $table->bigInteger('price');
            $table->bigInteger('price_trade_in')->nullable();
            $table->bigInteger('credit_monthly')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->json('attributes')->nullable();
            $table->string('status', 20)->default('draft');
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->integer('views_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('dealer_profile_id');
            $table->index('brand_id');
            $table->index('model_id');
            $table->index('status');
            $table->index('price');
            $table->index(['year', 'price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
