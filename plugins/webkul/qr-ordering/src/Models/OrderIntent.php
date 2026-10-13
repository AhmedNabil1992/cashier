<?php

namespace Webkul\QrOrdering\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderIntent extends Model
{
    protected $table = 'qr_ordering_order_intents';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['total' => 'decimal:4', 'payload' => 'array', 'submitted_at' => 'datetime', 'acknowledged_at' => 'datetime'];
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function guestSession(): BelongsTo
    {
        return $this->belongsTo(GuestSession::class);
    }
}
