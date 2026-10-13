<?php

namespace Webkul\RestaurantPos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Models\Product;

class ComboChoice extends Model
{
    protected $table = 'restaurant_pos_combo_choices';

    protected $fillable = ['combo_group_id', 'product_id', 'display_name', 'price_delta', 'is_default', 'is_active', 'sort'];

    protected function casts(): array
    {
        return ['price_delta'=>'decimal:4', 'is_default'=>'boolean', 'is_active'=>'boolean'];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ComboGroup::class, 'combo_group_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
