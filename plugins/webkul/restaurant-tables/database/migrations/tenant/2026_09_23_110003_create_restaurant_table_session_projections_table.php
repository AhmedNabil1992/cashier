<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_table_session_projections', function (Blueprint $table): void {
            $table->id();
            $table->uuid('edge_session_id')->unique();
            $table->foreignId('table_id')->constrained('restaurant_tables')->cascadeOnDelete();
            $table->uuid('edge_order_id')->nullable()->index();
            $table->string('waiter_reference')->nullable();
            $table->unsignedSmallInteger('guest_count')->default(1);
            $table->string('status');
            $table->unsignedBigInteger('version');
            $table->json('payload')->nullable();
            $table->timestamp('source_updated_at');
            $table->timestamp('last_synced_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_table_session_projections');
    }
};
