<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SyncOutboxMessage extends Model
{
    protected $fillable = [
        'message_id', 'target_edge_device_id', 'command_type', 'schema_version',
        'payload', 'status', 'attempts', 'available_at', 'acked_at', 'last_error',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $message): void {
            $message->message_id ??= (string) Str::uuid();
            $message->available_at ??= now();
        });
    }

    public function targetEdgeDevice(): BelongsTo
    {
        return $this->belongsTo(EdgeDevice::class, 'target_edge_device_id');
    }

    protected function casts(): array
    {
        return ['payload' => 'array', 'available_at' => 'datetime', 'acked_at' => 'datetime'];
    }
}
