<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_ordering_channels', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profile_id')->constrained('restaurant_pos_profiles')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('mode')->default('browse_and_order');
            $table->boolean('requires_table_token')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_ordering_channels');
    }
};
