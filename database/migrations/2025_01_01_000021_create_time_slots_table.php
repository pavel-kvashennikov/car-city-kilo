<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_profile_id')->constrained('service_profiles')->cascadeOnDelete();
            $table->foreignId('master_id')->nullable()->constrained('service_masters')->nullOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status', 20)->default('available');
            $table->foreignId('appointment_id')->nullable();
            $table->timestamps();

            $table->index(['service_profile_id', 'date', 'status']);
            $table->index(['master_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
