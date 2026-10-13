<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_drivers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('external_reference')->nullable();
            $table->string('status')->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'external_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_drivers');
    }
};
