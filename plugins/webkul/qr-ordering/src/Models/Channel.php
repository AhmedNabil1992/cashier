<?php

namespace Webkul\QrOrdering\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\RestaurantPos\Models\Profile;

class Channel extends Model
{
    protected $table = 'qr_ordering_channels';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['requires_table_token' => 'boolean', 'is_active' => 'boolean', 'settings' => 'array'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function guestSessions(): HasMany
    {
        return $this->hasMany(GuestSession::class);
    }

    public function orderIntents(): HasMany
    {
        return $this->hasMany(OrderIntent::class);
    }
}
