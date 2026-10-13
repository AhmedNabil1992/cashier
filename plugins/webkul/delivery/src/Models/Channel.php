<?php

namespace Webkul\Delivery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
    protected $table = 'delivery_channels';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['configuration' => 'encrypted:array', 'is_active' => 'boolean'];
    }

    public function externalOrders(): HasMany
    {
        return $this->hasMany(ExternalOrder::class);
    }
}
