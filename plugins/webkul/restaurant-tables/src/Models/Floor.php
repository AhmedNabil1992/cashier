<?php

namespace Webkul\RestaurantTables\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    protected $table = 'restaurant_floors';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function tables(): HasMany
    {
        return $this->hasMany(RestaurantTable::class);
    }
}
