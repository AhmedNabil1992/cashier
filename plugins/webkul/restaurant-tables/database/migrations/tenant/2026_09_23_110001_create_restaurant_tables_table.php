<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('floor_id')->constrained('restaurant_floors')->cascadeOnDelete();
            $table->uuid('locator')->unique();
            $table->string('code');
            $table->string('name');
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->string('shape')->default('square');
            $table->json('position')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['floor_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};
