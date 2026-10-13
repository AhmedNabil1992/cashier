<?php

namespace Webkul\QrOrdering\Actions;

use Illuminate\Validation\ValidationException;
use Webkul\QrOrdering\Models\Channel;

class QuoteOrderIntent
{
    public function execute(Channel $channel, array $attributes): array
    {
        $publication = $channel->profile->publications()->latest('version')->firstOrFail();

        if ((int) $publication->version !== (int) $attributes['catalog_version']) {
            abort(409, 'The catalog has changed. Reload the menu before submitting the order.');
        }

        if (! in_array($attributes['order_type'], $publication->payload['profile']['enabled_order_types'] ?? [], true)) {
            throw ValidationException::withMessages(['order_type' => 'The requested order type is not enabled for this POS profile.']);
        }

        $menuItems = collect($publication->payload['menus'] ?? [])
            ->filter(fn (array $menu): bool => in_array('qr', $menu['channels'] ?? [], true))
            ->flatMap(fn (array $menu) => collect($menu['categories'] ?? [])->flatMap(fn (array $category) => $category['items'] ?? []))
            ->keyBy('id');
        $modifierGroups = collect($publication->payload['modifier_groups'] ?? [])->keyBy('id');
        $modifierOptions = $modifierGroups
            ->flatMap(fn (array $group) => collect($group['options'] ?? [])->map(fn (array $option): array => $option + ['modifier_group_id' => $group['id']]))
            ->keyBy('id');
        $quotedItems = [];
        $total = 0.0;

        foreach ($attributes['items'] as $index => $requestedItem) {
            $item = $menuItems->get($requestedItem['menu_item_id']);

            if (! $item) {
                throw ValidationException::withMessages(["items.{$index}.menu_item_id" => 'The item is unavailable in the current QR catalog.']);
            }

            $price = $this->resolvePrice($item, $attributes['order_type']);
            $selectedOptions = collect($requestedItem['modifier_option_ids'] ?? [])->map(function (int $optionId) use ($item, $modifierOptions, $index): array {
                $option = $modifierOptions->get($optionId);

                if (! $option || ! in_array($option['modifier_group_id'], $item['modifier_group_ids'] ?? [], true)) {
                    throw ValidationException::withMessages(["items.{$index}.modifier_option_ids" => 'A selected modifier is not available for this item.']);
                }

                return $option;
            })->values();
            foreach ($item['modifier_group_ids'] ?? [] as $groupId) {
                $group = $modifierGroups->get($groupId);
                $selectionCount = $selectedOptions->where('modifier_group_id', $groupId)->count();

                if ($group && ($selectionCount < (int) $group['minimum_selections'] || $selectionCount > (int) $group['maximum_selections'])) {
                    throw ValidationException::withMessages(["items.{$index}.modifier_option_ids" => "Selections for modifier group {$group['name']} are outside its allowed range."]);
                }
            }

            $unitTotal = $price + $selectedOptions->sum(fn (array $option): float => (float) $option['price_delta']);
            $lineTotal = $unitTotal * $requestedItem['quantity'];
            $total += $lineTotal;
            $quotedItems[] = [
                'menu_item_id' => $item['id'],
                'product_id'   => $item['product_id'],
                'name'         => $item['name'],
                'quantity'     => $requestedItem['quantity'],
                'unit_price'   => round($price, 4),
                'modifiers'    => $selectedOptions->map(fn (array $option): array => [
                    'id' => $option['id'], 'name' => $option['name'], 'price_delta' => $option['price_delta'],
                ])->all(),
                'line_total' => round($lineTotal, 4),
            ];
        }

        return [
            'catalog_publication_id' => $publication->getKey(),
            'catalog_version'        => $publication->version,
            'order_type'             => $attributes['order_type'],
            'customer'               => $attributes['customer'] ?? null,
            'notes'                  => $attributes['notes'] ?? null,
            'items'                  => $quotedItems,
            'total'                  => round($total, 4),
        ];
    }

    private function resolvePrice(array $item, string $orderType): float
    {
        $now = now();
        $price = collect($item['prices'] ?? [])
            ->filter(fn (array $candidate): bool => ($candidate['channel'] === null || $candidate['channel'] === 'qr')
                && ($candidate['order_type'] === null || $candidate['order_type'] === $orderType)
                && (empty($candidate['starts_at']) || $now->greaterThanOrEqualTo($candidate['starts_at']))
                && (empty($candidate['ends_at']) || $now->lessThanOrEqualTo($candidate['ends_at'])))
            ->sortByDesc('priority')
            ->first();

        return (float) ($price['amount'] ?? $item['price']);
    }
}
