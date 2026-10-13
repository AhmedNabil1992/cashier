<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_reservations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('table_id')->nullable()->constrained('restaurant_tables')->nullOnDelete();
            $table->string('guest_name');
            $table->string('guest_phone')->nullable();
            $table->unsignedSmallInteger('party_size');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status')->default('pending');
            $table->decimal('deposit_amount', 15, 4)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_reservations');
    }
};
