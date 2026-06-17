<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_profile_id')->constrained('service_profiles')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->bigInteger('price_fixed')->nullable();
            $table->bigInteger('price_from')->nullable();
            $table->bigInteger('price_to')->nullable();
            $table->bigInteger('price_per_hour')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->json('vehicle_types')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('service_profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_items');
    }
};
