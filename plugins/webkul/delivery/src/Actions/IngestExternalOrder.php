<?php

namespace Webkul\Delivery\Actions;

use Illuminate\Support\Facades\DB;
use Webkul\Delivery\Models\Channel;
use Webkul\Delivery\Models\ExternalOrder;

class IngestExternalOrder
{
    public function execute(Channel $channel, array $attributes): ExternalOrder
    {
        return DB::transaction(function () use ($channel, $attributes): ExternalOrder {
            Channel::query()->lockForUpdate()->findOrFail($channel->getKey());
            $existing = $channel->externalOrders()->where('external_order_id', $attributes['external_order_id'])->first();
            if ($existing) {
                abort_if($existing->payload !== $attributes['payload'], 409, 'The external order ID was already used with a different payload.');

                return $existing;
            }

            return $channel->externalOrders()->create(['external_order_id'=>$attributes['external_order_id'], 'status'=>'pending_review', 'payload'=>$attributes['payload'], 'received_at'=>$attributes['received_at'] ?? now()]);
        });
    }
}
