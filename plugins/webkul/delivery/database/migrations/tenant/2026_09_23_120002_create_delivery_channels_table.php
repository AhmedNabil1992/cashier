<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_channels', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('provider');
            $table->string('external_store_id');
            $table->json('configuration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['provider', 'external_store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_channels');
    }
};
