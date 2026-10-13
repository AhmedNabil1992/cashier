<?php

namespace Webkul\RestaurantKds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketProjection extends Model
{
    protected $table = 'restaurant_kds_ticket_projections';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload'=>'array', 'source_created_at'=>'datetime', 'source_updated_at'=>'datetime', 'last_synced_at'=>'datetime'];
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
