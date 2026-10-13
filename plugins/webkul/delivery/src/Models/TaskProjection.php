<?php

namespace Webkul\Delivery\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProjection extends Model
{
    protected $table = 'delivery_task_projections';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload'=>'array', 'source_updated_at'=>'datetime', 'last_synced_at'=>'datetime'];
    }
}
