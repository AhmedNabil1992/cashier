<?php

namespace Webkul\RestaurantKds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;

class RoutingRule extends Model
{
    protected $table = 'restaurant_kds_routing_rules';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active'=>'boolean'];
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'product_category_id');
    }
}
