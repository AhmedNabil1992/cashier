<?php

namespace Webkul\RestaurantPos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Models\Product;

class ModifierOption extends Model
{
    protected $table = 'restaurant_pos_modifier_options';

    protected $fillable = ['modifier_group_id', 'product_id', 'nested_modifier_group_id', 'name', 'price_delta', 'is_active', 'sort'];

    protected function casts(): array
    {
        return ['price_delta'=>'decimal:4', 'is_active'=>'boolean'];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ModifierGroup::class, 'modifier_group_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function nestedGroup(): BelongsTo
    {
        return $this->belongsTo(ModifierGroup::class, 'nested_modifier_group_id');
    }
}
