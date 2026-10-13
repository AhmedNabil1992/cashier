<?php

namespace Webkul\QrOrdering\Actions;

use Illuminate\Support\Facades\DB;
use Webkul\QrOrdering\Models\OrderIntent;

class AcknowledgeOrderIntent
{
    public function execute(OrderIntent $orderIntent, array $attributes): array
    {
        return DB::transaction(function () use ($orderIntent, $attributes): array {
            $lockedIntent = OrderIntent::query()->lockForUpdate()->findOrFail($orderIntent->getKey());

            if ($lockedIntent->status !== 'pending_edge' || $lockedIntent->version >= $attributes['version']) {
                return ['applied' => false, 'order_intent' => $lockedIntent];
            }

            $lockedIntent->update([
                'status'           => $attributes['status'],
                'edge_order_id'    => $attributes['edge_order_id'] ?? null,
                'version'          => $attributes['version'],
                'rejection_reason' => $attributes['rejection_reason'] ?? null,
                'acknowledged_at'  => now(),
            ]);

            return ['applied' => true, 'order_intent' => $lockedIntent->refresh()];
        });
    }
}
