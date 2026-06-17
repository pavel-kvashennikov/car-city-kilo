<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_id')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('session_id');
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->morphs('itemable'); // product or appointment
            $table->integer('quantity')->default(1);
            $table->bigInteger('price');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('order_number', 20)->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->string('client_name');
            $table->string('client_phone', 20);
            $table->string('client_email')->nullable();
            $table->string('status', 20)->default('pending');
            $table->bigInteger('total_amount');
            $table->string('delivery_method', 20)->default('pickup'); // pickup, delivery
            $table->json('delivery_address')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('order_number');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->morphs('itemable');
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->bigInteger('price');
            $table->bigInteger('total');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
