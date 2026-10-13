<?php

namespace Webkul\RestaurantTables\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableSessionProjection extends Model
{
    protected $table = 'restaurant_table_session_projections';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload'=>'array', 'source_updated_at'=>'datetime', 'last_synced_at'=>'datetime'];
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }
}
