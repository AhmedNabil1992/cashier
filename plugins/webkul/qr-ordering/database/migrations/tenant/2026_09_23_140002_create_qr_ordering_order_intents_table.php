<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_ordering_order_intents', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('channel_id')->constrained('qr_ordering_channels')->cascadeOnDelete();
            $table->foreignId('guest_session_id')->nullable()->constrained('qr_ordering_guest_sessions')->nullOnDelete();
            $table->string('idempotency_key');
            $table->char('request_hash', 64);
            $table->string('status')->default('pending_edge');
            $table->unsignedBigInteger('catalog_version');
            $table->decimal('total', 15, 4);
            $table->json('payload');
            $table->uuid('edge_order_id')->nullable()->index();
            $table->unsignedBigInteger('version')->default(1);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
            $table->unique(['channel_id', 'idempotency_key']);
            $table->index(['status', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_ordering_order_intents');
    }
};
