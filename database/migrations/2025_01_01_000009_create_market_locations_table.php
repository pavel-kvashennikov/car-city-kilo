<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('market_zones');
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('code', 20);
            $table->string('type', 20)->default('box'); // pavilion, box, outdoor
            $table->string('status', 20)->default('available'); // available, occupied, reserved
            $table->json('coordinates')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('zone_id');
            $table->index('company_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_locations');
    }
};
