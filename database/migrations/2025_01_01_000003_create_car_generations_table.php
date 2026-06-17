<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('car_models');
            $table->string('name', 100)->nullable();
            $table->smallInteger('year_from')->nullable();
            $table->smallInteger('year_to')->nullable();
            $table->string('body_type', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_generations');
    }
};
