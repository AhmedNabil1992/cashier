<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_external_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('channel_id')->constrained('delivery_channels')->cascadeOnDelete();
            $table->string('external_order_id');
            $table->uuid('edge_order_id')->nullable();
            $table->string('status')->default('pending_review');
            $table->json('payload');
            $table->json('mapping_errors')->nullable();
            $table->timestamp('received_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
            $table->unique(['channel_id', 'external_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_external_orders');
    }
};
