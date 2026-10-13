<?php

namespace Webkul\RestaurantPos\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModifierGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->getKey(), 'profile_id'=>$this->profile_id, 'code'=>$this->code, 'name'=>$this->name,
            'minimum_selections'=> $this->minimum_selections, 'maximum_selections'=>$this->maximum_selections,
            'free_allowance'    => $this->free_allowance, 'is_required'=>$this->is_required, 'is_active'=>$this->is_active, 'sort'=>$this->sort,
            'options'           => $this->whenLoaded('options', fn () => $this->options->map(fn ($option): array => [
                'id'  => $option->getKey(), 'product_id'=>$option->product_id, 'nested_modifier_group_id'=>$option->nested_modifier_group_id,
                'name'=> $option->name, 'price_delta'=>$option->price_delta, 'is_active'=>$option->is_active, 'sort'=>$option->sort,
            ])->values()),
        ];
    }
}
