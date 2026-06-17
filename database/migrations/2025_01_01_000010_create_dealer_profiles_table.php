<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dealer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->json('working_hours')->nullable();
            $table->json('specializations')->nullable();
            $table->boolean('offers_credit')->default(false);
            $table->boolean('offers_trade_in')->default(false);
            $table->boolean('offers_test_drive')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dealer_profiles');
    }
};
