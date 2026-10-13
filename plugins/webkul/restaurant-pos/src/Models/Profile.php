<?php

namespace Webkul\RestaurantPos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Support\Models\Company;

class Profile extends Model
{
    protected $table = 'restaurant_pos_profiles';

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'default_order_type',
        'enabled_order_types',
        'settings',
        'is_active',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'enabled_order_types' => 'array',
            'settings'            => 'array',
            'is_active'           => 'boolean',
            'published_at'        => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(CatalogPublication::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class)->orderBy('sort')->orderBy('id');
    }

    public function modifierGroups(): HasMany
    {
        return $this->hasMany(ModifierGroup::class)->orderBy('sort')->orderBy('id');
    }
}
