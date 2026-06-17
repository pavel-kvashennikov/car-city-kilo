<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_profile_id')->constrained('service_profiles')->cascadeOnDelete();
            $table->string('name');
            $table->string('specialization')->nullable();
            $table->string('photo_path', 500)->nullable();
            $table->json('schedule')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('service_profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_masters');
    }
};
