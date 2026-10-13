<?php

namespace Webkul\RestaurantTables\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FloorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->getKey(), 'company_id' => $this->company_id, 'code' => $this->code, 'name' => $this->name, 'sort' => $this->sort, 'is_active' => $this->is_active, 'tables' => TableResource::collection($this->whenLoaded('tables'))];
    }
}
