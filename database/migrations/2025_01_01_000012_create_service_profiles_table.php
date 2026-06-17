<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('working_hours')->nullable();
            $table->json('specializations')->nullable();
            $table->json('vehicle_types')->nullable(); // ['car', 'truck', 'motorcycle']
            $table->integer('slot_duration_minutes')->default(60);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_profiles');
    }
};
