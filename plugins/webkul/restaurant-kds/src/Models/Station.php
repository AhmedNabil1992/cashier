<?php

namespace Webkul\RestaurantKds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\RestaurantPos\Models\Profile;

class Station extends Model
{
    protected $table = 'restaurant_kds_stations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['settings'=>'array', 'is_active'=>'boolean'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function routingRules(): HasMany
    {
        return $this->hasMany(RoutingRule::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(TicketProjection::class);
    }
}
