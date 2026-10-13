<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_pos_modifier_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profile_id')->constrained('restaurant_pos_profiles')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->unsignedSmallInteger('minimum_selections')->default(0);
            $table->unsignedSmallInteger('maximum_selections')->default(1);
            $table->unsignedSmallInteger('free_allowance')->default(0);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
            $table->unique(['profile_id', 'code']);
        });

        Schema::create('restaurant_pos_modifier_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('modifier_group_id')->constrained('restaurant_pos_modifier_groups')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products_products')->restrictOnDelete();
            $table->foreignId('nested_modifier_group_id')->nullable()->constrained('restaurant_pos_modifier_groups')->restrictOnDelete();
            $table->string('name');
            $table->decimal('price_delta', 15, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('restaurant_pos_menu_item_modifier_groups', function (Blueprint $table): void {
            $table->foreignId('menu_item_id')->constrained('restaurant_pos_menu_items')->cascadeOnDelete();
            $table->foreignId('modifier_group_id')->constrained('restaurant_pos_modifier_groups')->restrictOnDelete();
            $table->unsignedInteger('sort')->default(0);
            $table->primary(['menu_item_id', 'modifier_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_pos_menu_item_modifier_groups');
        Schema::dropIfExists('restaurant_pos_modifier_options');
        Schema::dropIfExists('restaurant_pos_modifier_groups');
    }
};
