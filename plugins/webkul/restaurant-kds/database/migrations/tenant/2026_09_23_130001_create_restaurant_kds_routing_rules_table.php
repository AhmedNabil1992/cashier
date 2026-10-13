<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_kds_routing_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('station_id')->constrained('restaurant_kds_stations')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products_products')->cascadeOnDelete();
            $table->foreignId('product_category_id')->nullable()->constrained('products_categories')->cascadeOnDelete();
            $table->string('order_type')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_kds_routing_rules');
    }
};
