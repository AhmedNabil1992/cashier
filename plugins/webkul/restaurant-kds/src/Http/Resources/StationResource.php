<?php

namespace Webkul\RestaurantKds\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->getKey(),
            'profile_id'    => $this->profile_id,
            'code'          => $this->code,
            'name'          => $this->name,
            'display_mode'  => $this->display_mode,
            'target_seconds'=> $this->target_seconds,
            'settings'      => $this->settings ?? [],
            'is_active'     => $this->is_active,
            'sort'          => $this->sort,
            'routing_rules' => $this->whenLoaded('routingRules', fn () => $this->routingRules->map(fn ($rule): array => [
                'id'                  => $rule->getKey(),
                'product_id'          => $rule->product_id,
                'product_category_id' => $rule->product_category_id,
                'order_type'          => $rule->order_type,
                'priority'            => $rule->priority,
                'is_active'           => $rule->is_active,
            ])->values()),
        ];
    }
}
