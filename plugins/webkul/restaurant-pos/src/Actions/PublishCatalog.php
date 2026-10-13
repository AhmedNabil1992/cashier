<?php

namespace Webkul\RestaurantPos\Actions;

use Illuminate\Support\Facades\DB;
use Webkul\Product\Models\Product;
use Webkul\RestaurantPos\Models\CatalogPublication;
use Webkul\RestaurantPos\Models\Profile;
use Webkul\Support\Models\Scopes\CompanyScope;

class PublishCatalog
{
    public function execute(Profile $profile): CatalogPublication
    {
        return DB::transaction(function () use ($profile): CatalogPublication {
            $lockedProfile = Profile::query()->lockForUpdate()->findOrFail($profile->getKey());
            $nextVersion = ((int) $lockedProfile->publications()->max('version')) + 1;
            $publishedAt = now();
            $modifierGroups = $lockedProfile->modifierGroups()
                ->where('is_active', true)
                ->with(['options'=>fn ($query) => $query->where('is_active', true)])
                ->get()
                ->map(fn ($group): array => [
                    'id'                 => $group->getKey(),
                    'code'               => $group->code,
                    'name'               => $group->name,
                    'minimum_selections' => $group->minimum_selections,
                    'maximum_selections' => $group->maximum_selections,
                    'free_allowance'     => $group->free_allowance,
                    'is_required'        => $group->is_required,
                    'options'            => $group->options->map(fn ($option): array => [
                        'id'                       => $option->getKey(),
                        'product_id'               => $option->product_id,
                        'nested_modifier_group_id' => $option->nested_modifier_group_id,
                        'name'                     => $option->name,
                        'price_delta'              => $option->price_delta,
                    ])->values()->all(),
                ])->all();
            $menus = $lockedProfile->menus()
                ->where('is_active', true)
                ->with([
                    'categories'      => fn ($query) => $query->where('is_active', true),
                    'categories.items'=> fn ($query) => $query->where('is_active', true),
                    'categories.items.product',
                    'categories.items.modifierGroups',
                    'categories.items.prices'             => fn ($query) => $query->where('is_active', true),
                    'categories.items.comboGroups.choices'=> fn ($query) => $query->where('is_active', true),
                    'categories.items.comboGroups.choices.product',
                ])
                ->get()
                ->map(fn ($menu): array => [
                    'id'           => $menu->getKey(),
                    'code'         => $menu->code,
                    'name'         => $menu->name,
                    'channels'     => $menu->channels,
                    'availability' => $menu->availability ?? [],
                    'categories'   => $menu->categories->map(fn ($category): array => [
                        'id'           => $category->getKey(),
                        'name'         => $category->name,
                        'description'  => $category->description,
                        'image'        => $category->image,
                        'availability' => $category->availability ?? [],
                        'items'        => $category->items
                            ->filter(fn ($item): bool => $item->product?->enable_sales === true)
                            ->map(fn ($item): array => [
                                'id'                 => $item->getKey(),
                                'product_id'         => $item->product_id,
                                'name'               => $item->display_name ?: $item->product->name,
                                'description'        => $item->description,
                                'image'              => $item->image,
                                'price'              => $item->price_override ?? $item->product->price,
                                'reference'          => $item->product->reference,
                                'barcode'            => $item->product->barcode,
                                'availability'       => $item->availability ?? [],
                                'modifier_group_ids' => $item->modifierGroups->pluck('id')->all(),
                                'prices'             => $item->prices->map(fn ($price): array => [
                                    'channel'  => $price->channel, 'order_type'=>$price->order_type, 'amount'=>$price->amount,
                                    'starts_at'=> $price->starts_at?->toIso8601String(), 'ends_at'=>$price->ends_at?->toIso8601String(), 'priority'=>$price->priority,
                                ])->values()->all(),
                                'combo_groups' => $item->comboGroups->map(fn ($group): array => [
                                    'id'                => $group->getKey(), 'code'=>$group->code, 'name'=>$group->name,
                                    'minimum_selections'=> $group->minimum_selections, 'maximum_selections'=>$group->maximum_selections,
                                    'choices'           => $group->choices->map(fn ($choice): array => [
                                        'id'         => $choice->getKey(), 'product_id'=>$choice->product_id,
                                        'name'       => $choice->display_name ?: $choice->product?->name,
                                        'price_delta'=> $choice->price_delta, 'is_default'=>$choice->is_default,
                                    ])->values()->all(),
                                ])->values()->all(),
                            ])->values()->all(),
                    ])->values()->all(),
                ])->all();
            $payload = [
                'profile' => [
                    'id'                  => $lockedProfile->getKey(),
                    'code'                => $lockedProfile->code,
                    'name'                => $lockedProfile->name,
                    'company_id'          => $lockedProfile->company_id,
                    'default_order_type'  => $lockedProfile->default_order_type,
                    'enabled_order_types' => $lockedProfile->enabled_order_types,
                    'settings'            => $lockedProfile->settings ?? [],
                ],
                'modifier_groups' => $modifierGroups,
                'menus'           => $menus,
                'products'        => Product::query()
                    ->withoutGlobalScope(CompanyScope::class)
                    ->with('category:id,name')
                    ->where('enable_sales', true)
                    ->where(function ($query) use ($lockedProfile): void {
                        $query->whereNull('company_id')->orWhere('company_id', $lockedProfile->company_id);
                    })
                    ->orderBy('id')
                    ->get()
                    ->map(fn (Product $product): array => [
                        'id'              => $product->getKey(),
                        'name'            => $product->name,
                        'reference'       => $product->reference,
                        'barcode'         => $product->barcode,
                        'price'           => $product->price,
                        'parent_id'       => $product->parent_id,
                        'is_configurable' => (bool) $product->is_configurable,
                        'category_id'     => $product->category_id,
                        'category'        => $product->category?->name,
                        'updated_at'      => $product->updated_at?->toIso8601String(),
                    ])->all(),
                'published_at' => $publishedAt->toIso8601String(),
                'version'      => $nextVersion,
            ];
            $encodedPayload = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $publication = $lockedProfile->publications()->create([
                'version'      => $nextVersion,
                'checksum'     => hash('sha256', $encodedPayload),
                'payload'      => $payload,
                'published_at' => $publishedAt,
            ]);
            $lockedProfile->update(['published_at' => $publishedAt]);

            return $publication;
        });
    }
}
