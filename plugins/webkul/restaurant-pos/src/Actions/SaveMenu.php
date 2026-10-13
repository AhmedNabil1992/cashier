<?php

namespace Webkul\RestaurantPos\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\RestaurantPos\Models\Menu;
use Webkul\RestaurantPos\Models\Profile;

class SaveMenu
{
    /** @param array<string, mixed> $attributes */
    public function execute(Profile $profile, array $attributes, ?Menu $menu = null): Menu
    {
        return DB::transaction(function () use ($profile, $attributes, $menu): Menu {
            $menu ??= new Menu(['profile_id'=>$profile->getKey()]);
            abort_unless((int) $menu->profile_id === (int) $profile->getKey(), 404);
            $menu->fill(Arr::except($attributes, ['categories']))->save();
            $categoryIds = [];

            foreach ($attributes['categories'] as $categoryData) {
                $categoryId = Arr::pull($categoryData, 'id');
                $items = Arr::pull($categoryData, 'items');
                $category = $categoryId ? $menu->categories()->findOrFail($categoryId) : $menu->categories()->make();
                $category->fill($categoryData)->save();
                $categoryIds[] = $category->getKey();
                $itemIds = [];

                foreach ($items as $itemData) {
                    $itemId = Arr::pull($itemData, 'id');
                    $modifierGroups = Arr::pull($itemData, 'modifier_groups', []);
                    $prices = Arr::pull($itemData, 'prices', []);
                    $comboGroups = Arr::pull($itemData, 'combo_groups', []);
                    $item = $itemId ? $category->items()->findOrFail($itemId) : $category->items()->make();
                    $item->fill($itemData)->save();
                    $item->modifierGroups()->sync(collect($modifierGroups)->mapWithKeys(
                        fn (array $modifierGroup): array => [$modifierGroup['id']=>['sort'=>$modifierGroup['sort'] ?? 0]],
                    ));
                    $this->syncPrices($item, $prices);
                    $this->syncComboGroups($item, $comboGroups);
                    $itemIds[] = $item->getKey();
                }

                $category->items()->whereNotIn('id', $itemIds)->delete();
            }

            $menu->categories()->whereNotIn('id', $categoryIds)->delete();

            return $menu->refresh()->load('categories.items.product', 'categories.items.modifierGroups', 'categories.items.prices', 'categories.items.comboGroups.choices');
        });
    }

    /** @param array<int, array<string, mixed>> $prices */
    private function syncPrices($item, array $prices): void
    {
        foreach ($prices as $price) {
            if (isset($price['starts_at'], $price['ends_at'])
                && CarbonImmutable::parse($price['starts_at']) >= CarbonImmutable::parse($price['ends_at'])) {
                throw ValidationException::withMessages(['categories'=>'A price schedule must end after it starts.']);
            }
        }

        foreach ($prices as $index => $left) {
            foreach (array_slice($prices, $index + 1) as $right) {
                if (($left['channel'] ?? null) !== ($right['channel'] ?? null)
                    || ($left['order_type'] ?? null) !== ($right['order_type'] ?? null)
                    || ($left['priority'] ?? 0) !== ($right['priority'] ?? 0)) {
                    continue;
                }

                $leftStart = isset($left['starts_at']) ? CarbonImmutable::parse($left['starts_at']) : CarbonImmutable::create(1);
                $leftEnd = isset($left['ends_at']) ? CarbonImmutable::parse($left['ends_at']) : CarbonImmutable::create(9999, 12, 31);
                $rightStart = isset($right['starts_at']) ? CarbonImmutable::parse($right['starts_at']) : CarbonImmutable::create(1);
                $rightEnd = isset($right['ends_at']) ? CarbonImmutable::parse($right['ends_at']) : CarbonImmutable::create(9999, 12, 31);

                if ($leftStart < $rightEnd && $rightStart < $leftEnd) {
                    throw ValidationException::withMessages(['categories'=>'Price schedules with the same channel, order type, and priority cannot overlap.']);
                }
            }
        }

        $ids = [];
        foreach ($prices as $data) {
            $id = Arr::pull($data, 'id');
            $price = $id ? $item->prices()->findOrFail($id) : $item->prices()->make();
            $price->fill($data)->save();
            $ids[] = $price->getKey();
        }
        $item->prices()->whereNotIn('id', $ids)->delete();
    }

    /** @param array<int, array<string, mixed>> $groups */
    private function syncComboGroups($item, array $groups): void
    {
        $groupIds = [];
        foreach ($groups as $groupData) {
            $id = Arr::pull($groupData, 'id');
            $choices = Arr::pull($groupData, 'choices');
            if ((int) $groupData['minimum_selections'] > (int) $groupData['maximum_selections']) {
                throw ValidationException::withMessages(['categories'=>'Combo minimum selections cannot exceed maximum selections.']);
            }
            $group = $id ? $item->comboGroups()->findOrFail($id) : $item->comboGroups()->make();
            $group->fill($groupData)->save();
            $groupIds[] = $group->getKey();
            $choiceIds = [];
            foreach ($choices as $choiceData) {
                $choiceId = Arr::pull($choiceData, 'id');
                $choice = $choiceId ? $group->choices()->findOrFail($choiceId) : $group->choices()->make();
                $choice->fill($choiceData)->save();
                $choiceIds[] = $choice->getKey();
            }
            $group->choices()->whereNotIn('id', $choiceIds)->delete();
        }
        $item->comboGroups()->whereNotIn('id', $groupIds)->delete();
    }
}
