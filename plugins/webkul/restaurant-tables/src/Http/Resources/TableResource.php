<?php

namespace Webkul\RestaurantTables\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->getKey(), 'floor_id' => $this->floor_id, 'locator' => $this->locator, 'code' => $this->code, 'name' => $this->name, 'capacity' => $this->capacity, 'shape' => $this->shape, 'position' => $this->position, 'is_active' => $this->is_active];
    }
}
