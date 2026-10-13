<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_kds_ticket_projections', function (Blueprint $table): void {
            $table->id();
            $table->uuid('edge_ticket_id')->unique();
            $table->uuid('edge_order_id');
            $table->foreignId('station_id')->constrained('restaurant_kds_stations')->cascadeOnDelete();
            $table->string('status');
            $table->unsignedSmallInteger('priority')->default(0);
            $table->unsignedBigInteger('version');
            $table->json('payload')->nullable();
            $table->timestamp('source_created_at');
            $table->timestamp('source_updated_at');
            $table->timestamp('last_synced_at');
            $table->timestamps();
            $table->index(['station_id', 'status', 'source_updated_at'], 'restaurant_kds_live_board');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_kds_ticket_projections');
    }
};
