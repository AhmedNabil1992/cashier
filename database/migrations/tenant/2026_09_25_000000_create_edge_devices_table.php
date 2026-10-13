<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edge_devices', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('name');
            $table->string('type', 32)->index();
            $table->string('status', 16)->default('active')->index();
            $table->json('abilities');
            $table->unsignedInteger('token_version')->default(1);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edge_devices');
    }
};
