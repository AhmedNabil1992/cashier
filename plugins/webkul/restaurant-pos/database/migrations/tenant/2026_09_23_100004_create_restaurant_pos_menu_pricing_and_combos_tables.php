<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_pos_menu_item_prices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_item_id')->constrained('restaurant_pos_menu_items')->cascadeOnDelete();
            $table->string('channel')->nullable();
            $table->string('order_type')->nullable();
            $table->decimal('amount', 15, 4);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['menu_item_id', 'channel', 'order_type', 'is_active'], 'restaurant_pos_item_price_lookup');
        });

        Schema::create('restaurant_pos_combo_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_item_id')->constrained('restaurant_pos_menu_items')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->unsignedSmallInteger('minimum_selections')->default(1);
            $table->unsignedSmallInteger('maximum_selections')->default(1);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
            $table->unique(['menu_item_id', 'code']);
        });

        Schema::create('restaurant_pos_combo_choices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('combo_group_id')->constrained('restaurant_pos_combo_groups')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products_products')->restrictOnDelete();
            $table->string('display_name')->nullable();
            $table->decimal('price_delta', 15, 4)->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
            $table->unique(['combo_group_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_pos_combo_choices');
        Schema::dropIfExists('restaurant_pos_combo_groups');
        Schema::dropIfExists('restaurant_pos_menu_item_prices');
    }
};
