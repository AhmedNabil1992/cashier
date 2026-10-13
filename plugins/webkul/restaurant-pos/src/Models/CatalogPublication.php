<?php

namespace Webkul\RestaurantPos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogPublication extends Model
{
    protected $table = 'restaurant_pos_catalog_publications';

    protected $fillable = [
        'profile_id',
        'version',
        'checksum',
        'payload',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'payload'      => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
