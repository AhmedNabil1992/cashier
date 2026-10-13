<?php

namespace Webkul\RestaurantPos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $table = 'restaurant_pos_menus';

    protected $fillable = ['profile_id', 'code', 'name', 'channels', 'availability', 'is_active', 'sort'];

    protected function casts(): array
    {
        return ['channels'=>'array', 'availability'=>'array', 'is_active'=>'boolean'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(MenuCategory::class)->orderBy('sort')->orderBy('id');
    }
}
