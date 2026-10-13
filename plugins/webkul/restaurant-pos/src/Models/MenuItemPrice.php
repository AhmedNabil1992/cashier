<?php

namespace Webkul\RestaurantPos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItemPrice extends Model
{
    protected $table = 'restaurant_pos_menu_item_prices';

    protected $fillable = ['menu_item_id', 'channel', 'order_type', 'amount', 'starts_at', 'ends_at', 'priority', 'is_active'];

    protected function casts(): array
    {
        return ['amount'=>'decimal:4', 'starts_at'=>'datetime', 'ends_at'=>'datetime', 'is_active'=>'boolean'];
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
