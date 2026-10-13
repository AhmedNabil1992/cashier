<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_outbox_messages', function (Blueprint $table): void {
            $table->id();
            $table->uuid('message_id')->unique();
            $table->foreignId('target_edge_device_id')->nullable()->constrained('edge_devices')->nullOnDelete();
            $table->string('command_type', 150);
            $table->unsignedSmallInteger('schema_version');
            $table->json('payload');
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('available_at')->index();
            $table->timestamp('acked_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_outbox_messages');
    }
};
