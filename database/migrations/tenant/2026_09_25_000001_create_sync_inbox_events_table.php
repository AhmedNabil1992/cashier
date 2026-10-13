<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_inbox_events', function (Blueprint $table): void {
            $table->id();
            $table->uuid('event_id')->unique();
            $table->foreignId('source_edge_device_id')->constrained('edge_devices')->restrictOnDelete();
            $table->string('aggregate_type', 100);
            $table->string('aggregate_id', 100);
            $table->unsignedBigInteger('aggregate_version');
            $table->string('event_type', 150);
            $table->unsignedSmallInteger('schema_version');
            $table->json('payload');
            $table->char('request_hash', 64);
            $table->string('status', 20)->default('received')->index();
            $table->timestamp('occurred_at');
            $table->timestamp('applied_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['aggregate_type', 'aggregate_id', 'aggregate_version'], 'sync_inbox_aggregate_version_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_inbox_events');
    }
};
