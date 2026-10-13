<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_pos_menus', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profile_id')->constrained('restaurant_pos_profiles')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->json('channels');
            $table->json('availability')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
            $table->unique(['profile_id', 'code']);
        });

        Schema::create('restaurant_pos_menu_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_id')->constrained('restaurant_pos_menus')->cascadeOnDelete();
            $table->foreignId('product_category_id')->nullable()->constrained('products_categories')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->json('availability')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('restaurant_pos_menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_category_id')->constrained('restaurant_pos_menu_categories')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products_products')->restrictOnDelete();
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price_override', 15, 4)->nullable();
            $table->json('availability')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
            $table->unique(['menu_category_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_pos_menu_items');
        Schema::dropIfExists('restaurant_pos_menu_categories');
        Schema::dropIfExists('restaurant_pos_menus');
    }
};
