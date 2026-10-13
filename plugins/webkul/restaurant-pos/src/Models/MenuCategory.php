<?php

namespace Webkul\RestaurantPos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Product\Models\Category;

class MenuCategory extends Model
{
    protected $table = 'restaurant_pos_menu_categories';

    protected $fillable = ['menu_id', 'product_category_id', 'name', 'description', 'image', 'availability', 'is_active', 'sort'];

    protected function casts(): array
    {
        return ['availability'=>'array', 'is_active'=>'boolean'];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'product_category_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort')->orderBy('id');
    }
}
