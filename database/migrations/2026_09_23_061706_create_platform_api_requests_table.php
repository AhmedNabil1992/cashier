<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('platform_api_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('idempotency_key')->unique();
            $table->string('method', 10);
            $table->string('path');
            $table->char('request_hash', 64);
            $table->unsignedSmallInteger('response_status');
            $table->json('response_body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_api_requests');
    }
};
