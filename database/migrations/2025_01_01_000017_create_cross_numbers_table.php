<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cross_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('brand', 100)->nullable();
            $table->string('number', 100);
            $table->boolean('is_oem')->default(false);
            $table->timestamps();

            $table->index(['product_id']);
            $table->index(['number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cross_numbers');
    }
};
