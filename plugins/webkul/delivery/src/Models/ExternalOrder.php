<?php

namespace Webkul\Delivery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalOrder extends Model
{
    protected $table = 'delivery_external_orders';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'mapping_errors' => 'array', 'received_at' => 'datetime', 'acknowledged_at' => 'datetime'];
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
