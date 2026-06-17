<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('car_brands');
            $table->string('name', 100);
            $table->string('slug', 150)->unique();
            $table->boolean('is_popular')->default(false);

            $table->index('brand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_models');
    }
};
