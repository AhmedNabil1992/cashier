<?php

namespace Webkul\RestaurantTables\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    protected $table = 'restaurant_tables';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['position'=>'array', 'is_active'=>'boolean'];
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'table_id');
    }
}
