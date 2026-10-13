<?php

namespace Webkul\Delivery\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'delivery_drivers';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
