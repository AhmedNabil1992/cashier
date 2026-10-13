<?php

namespace Webkul\QrOrdering\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestSession extends Model
{
    protected $table = 'qr_ordering_guest_sessions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'last_seen_at' => 'datetime', 'revoked_at' => 'datetime', 'metadata' => 'array'];
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function orderIntents(): HasMany
    {
        return $this->hasMany(OrderIntent::class);
    }

    public function isUsable(): bool
    {
        return $this->revoked_at === null && $this->expires_at->isFuture();
    }
}
