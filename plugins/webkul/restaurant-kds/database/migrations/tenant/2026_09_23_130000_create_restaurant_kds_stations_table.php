<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_kds_stations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profile_id')->constrained('restaurant_pos_profiles')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('display_mode')->default('station');
            $table->unsignedInteger('target_seconds')->default(600);
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
            $table->unique(['profile_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_kds_stations');
    }
};
