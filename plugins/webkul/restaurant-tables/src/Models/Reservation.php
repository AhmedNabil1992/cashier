<?php

namespace Webkul\RestaurantTables\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $table = 'restaurant_reservations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'deposit_amount' => 'decimal:4'];
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class);
    }
}
