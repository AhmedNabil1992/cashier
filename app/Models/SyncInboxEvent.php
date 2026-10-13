<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncInboxEvent extends Model
{
    protected $fillable = [
        'event_id', 'source_edge_device_id', 'aggregate_type', 'aggregate_id',
        'aggregate_version', 'event_type', 'schema_version', 'payload',
        'request_hash', 'status', 'occurred_at', 'applied_at', 'error',
    ];

    public function sourceEdgeDevice(): BelongsTo
    {
        return $this->belongsTo(EdgeDevice::class, 'source_edge_device_id');
    }

    protected function casts(): array
    {
        return ['payload' => 'array', 'occurred_at' => 'datetime', 'applied_at' => 'datetime'];
    }
}
