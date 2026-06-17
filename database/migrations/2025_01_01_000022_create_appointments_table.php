<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('service_profile_id')->constrained('service_profiles')->cascadeOnDelete();
            $table->foreignId('service_item_id')->nullable()->constrained('service_items')->nullOnDelete();
            $table->foreignId('master_id')->nullable()->constrained('service_masters')->nullOnDelete();
            $table->foreignId('time_slot_id')->nullable()->constrained('time_slots')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('client_name');
            $table->string('client_phone', 20);
            $table->string('client_email')->nullable();
            $table->string('vehicle_brand')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->smallInteger('vehicle_year')->nullable();
            $table->string('vehicle_vin', 17)->nullable();
            $table->text('comment')->nullable();
            $table->string('status', 20)->default('pending');
            $table->bigInteger('estimated_cost')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();

            $table->index(['service_profile_id', 'status']);
            $table->index('user_id');
        });

        // Add FK from time_slots to appointments
        Schema::table('time_slots', function (Blueprint $table) {
            $table->foreign('appointment_id')->references('id')->on('appointments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('time_slots', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
        });
        Schema::dropIfExists('appointments');
    }
};
