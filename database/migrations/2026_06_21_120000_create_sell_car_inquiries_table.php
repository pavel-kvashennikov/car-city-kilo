<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sell_car_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('car_info');
            $table->unsignedInteger('mileage')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('client_phone', 30);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('new'); // new, in_progress, done
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sell_car_inquiries');
    }
};
