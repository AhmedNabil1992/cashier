<?php

namespace Webkul\RestaurantPos\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->getKey(),
            'profile_id'   => $this->profile_id,
            'code'         => $this->code,
            'name'         => $this->name,
            'channels'     => $this->channels,
            'availability' => $this->availability ?? [],
            'is_active'    => $this->is_active,
            'sort'         => $this->sort,
            'categories'   => $this->whenLoaded('categories', fn () => $this->categories->map(fn ($category): array => [
                'id'                  => $category->getKey(),
                'product_category_id' => $category->product_category_id,
                'name'                => $category->name,
                'description'         => $category->description,
                'image'               => $category->image,
                'availability'        => $category->availability ?? [],
                'is_active'           => $category->is_active,
                'sort'                => $category->sort,
                'items'               => $category->items->map(fn ($item): array => [
                    'id'              => $item->getKey(),
                    'product_id'      => $item->product_id,
                    'display_name'    => $item->display_name,
                    'description'     => $item->description,
                    'image'           => $item->image,
                    'price_override'  => $item->price_override,
                    'availability'    => $item->availability ?? [],
                    'is_active'       => $item->is_active,
                    'sort'            => $item->sort,
                    'modifier_groups' => $item->whenLoaded('modifierGroups', fn () => $item->modifierGroups->map(fn ($group): array => [
                        'id'   => $group->getKey(),
                        'sort' => $group->pivot->sort,
                    ])->values()),
                    'prices' => $item->whenLoaded('prices', fn () => $item->prices->map(fn ($price): array => [
                        'id'      => $price->getKey(), 'channel'=>$price->channel, 'order_type'=>$price->order_type,
                        'amount'  => $price->amount, 'starts_at'=>$price->starts_at?->toIso8601String(), 'ends_at'=>$price->ends_at?->toIso8601String(),
                        'priority'=> $price->priority, 'is_active'=>$price->is_active,
                    ])->values()),
                    'combo_groups' => $item->whenLoaded('comboGroups', fn () => $item->comboGroups->map(fn ($group): array => [
                        'id'                => $group->getKey(), 'code'=>$group->code, 'name'=>$group->name,
                        'minimum_selections'=> $group->minimum_selections, 'maximum_selections'=>$group->maximum_selections, 'sort'=>$group->sort,
                        'choices'           => $group->choices->map(fn ($choice): array => [
                            'id'         => $choice->getKey(), 'product_id'=>$choice->product_id, 'display_name'=>$choice->display_name,
                            'price_delta'=> $choice->price_delta, 'is_default'=>$choice->is_default, 'is_active'=>$choice->is_active, 'sort'=>$choice->sort,
                        ])->values(),
                    ])->values()),
                ])->values(),
            ])->values()),
        ];
    }
}
