<?php

namespace Webkul\RestaurantPos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComboGroup extends Model
{
    protected $table = 'restaurant_pos_combo_groups';

    protected $fillable = ['menu_item_id', 'code', 'name', 'minimum_selections', 'maximum_selections', 'sort'];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(ComboChoice::class)->orderBy('sort')->orderBy('id');
    }
}
