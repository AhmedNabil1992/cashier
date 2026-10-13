<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_ordering_guest_sessions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('channel_id')->constrained('qr_ordering_channels')->cascadeOnDelete();
            $table->char('token_hash', 64)->unique();
            $table->string('table_reference')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['channel_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_ordering_guest_sessions');
    }
};
