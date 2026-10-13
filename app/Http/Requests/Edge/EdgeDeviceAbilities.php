<?php

namespace App\Http\Requests\Edge;

final class EdgeDeviceAbilities
{
    public static function all(): array
    {
        return [
            'edge:sync',
            'restaurant-tables:sync',
            'restaurant-kds:sync',
            'delivery:sync',
            'delivery:ingest',
            'qr-ordering:sync',
        ];
    }
}
