<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_applicabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('car_brands');
            $table->foreignId('model_id')->nullable()->constrained('car_models');
            $table->smallInteger('year_from')->nullable();
            $table->smallInteger('year_to')->nullable();

            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_applicabilities');
    }
};
