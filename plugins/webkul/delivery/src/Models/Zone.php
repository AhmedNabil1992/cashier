<?php

namespace Webkul\Delivery\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $table = 'delivery_zones';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['geometry' => 'array', 'delivery_fee' => 'decimal:4', 'minimum_order' => 'decimal:4', 'is_active' => 'boolean'];
    }
}
