<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_pos_catalog_publications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profile_id')->constrained('restaurant_pos_profiles')->cascadeOnDelete();
            $table->unsignedBigInteger('version');
            $table->char('checksum', 64);
            $table->json('payload');
            $table->timestamp('published_at');
            $table->timestamps();
            $table->unique(['profile_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_pos_catalog_publications');
    }
};
