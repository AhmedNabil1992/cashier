<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class PlatformApiRequest extends Model
{
    use CentralConnection;

    protected $fillable = [
        'idempotency_key', 'method', 'path', 'request_hash', 'response_status', 'response_body',
    ];

    protected function casts(): array
    {
        return ['response_body' => 'array'];
    }
}
