<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dealer_leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('dealer_profile_id')->constrained('dealer_profiles')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('lead_type', 20); // test_drive, credit, trade_in, callback
            $table->string('client_name');
            $table->string('client_phone', 20);
            $table->string('client_email')->nullable();
            $table->timestamp('preferred_datetime')->nullable();
            $table->json('trade_in_data')->nullable();
            $table->json('credit_data')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('new'); // new, in_progress, completed, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('dealer_profile_id');
            $table->index('status');
            $table->index('lead_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dealer_leads');
    }
};
