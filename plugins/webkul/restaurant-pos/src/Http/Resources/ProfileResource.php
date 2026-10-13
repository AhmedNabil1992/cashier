<?php

namespace Webkul\RestaurantPos\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->getKey(),
            'company_id'          => $this->company_id,
            'code'                => $this->code,
            'name'                => $this->name,
            'default_order_type'  => $this->default_order_type,
            'enabled_order_types' => $this->enabled_order_types,
            'settings'            => $this->settings ?? [],
            'is_active'           => $this->is_active,
            'published_at'        => $this->published_at?->toIso8601String(),
        ];
    }
}
