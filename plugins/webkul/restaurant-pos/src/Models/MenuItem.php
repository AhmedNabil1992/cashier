<?php

namespace Webkul\RestaurantPos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Product\Models\Product;

class MenuItem extends Model
{
    protected $table = 'restaurant_pos_menu_items';

    protected $fillable = ['menu_category_id', 'product_id', 'display_name', 'description', 'image', 'price_override', 'availability', 'is_active', 'sort'];

    protected function casts(): array
    {
        return ['price_override'=>'decimal:4', 'availability'=>'array', 'is_active'=>'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function modifierGroups(): BelongsToMany
    {
        return $this->belongsToMany(ModifierGroup::class, 'restaurant_pos_menu_item_modifier_groups')
            ->withPivot('sort')
            ->orderByPivot('sort');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(MenuItemPrice::class)->orderByDesc('priority')->orderBy('id');
    }

    public function comboGroups(): HasMany
    {
        return $this->hasMany(ComboGroup::class)->orderBy('sort')->orderBy('id');
    }
}
