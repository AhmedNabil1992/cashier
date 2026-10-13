<?php

namespace Webkul\RestaurantPos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModifierGroup extends Model
{
    protected $table = 'restaurant_pos_modifier_groups';

    protected $fillable = ['profile_id', 'code', 'name', 'minimum_selections', 'maximum_selections', 'free_allowance', 'is_required', 'is_active', 'sort'];

    protected function casts(): array
    {
        return ['is_required'=>'boolean', 'is_active'=>'boolean'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ModifierOption::class)->orderBy('sort')->orderBy('id');
    }

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'restaurant_pos_menu_item_modifier_groups')->withPivot('sort');
    }
}
