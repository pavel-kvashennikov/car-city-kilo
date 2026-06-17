<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('slug')->nullable();
            $table->foreignId('parts_profile_id')->constrained('parts_profiles')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('part_categories')->nullOnDelete();
            $table->string('name', 500);
            $table->string('article_number', 100)->nullable();
            $table->string('oem_number', 100)->nullable();
            $table->string('barcode', 50)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('condition', 20)->default('new');
            $table->string('part_type', 20)->default('aftermarket');
            $table->bigInteger('price_retail');
            $table->bigInteger('price_wholesale')->nullable();
            $table->smallInteger('wholesale_min_qty')->default(1);
            $table->text('description')->nullable();
            $table->json('attributes')->nullable();
            $table->string('status', 20)->default('active');
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('parts_profile_id');
            $table->index('category_id');
            $table->index('oem_number');
            $table->index('article_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
