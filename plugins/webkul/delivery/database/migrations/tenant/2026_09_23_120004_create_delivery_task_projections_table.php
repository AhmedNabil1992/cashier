<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_task_projections', function (Blueprint $table): void {
            $table->id();
            $table->uuid('edge_task_id')->unique();
            $table->uuid('edge_order_id')->index();
            $table->foreignId('driver_id')->nullable()->constrained('delivery_drivers')->nullOnDelete();
            $table->string('status');
            $table->unsignedBigInteger('version');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('source_updated_at');
            $table->timestamp('last_synced_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_task_projections');
    }
};
