<?php

namespace Webkul\Delivery\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExternalOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id'=>$this->getKey(), 'channel_id'=>$this->channel_id, 'external_order_id'=>$this->external_order_id, 'edge_order_id'=>$this->edge_order_id, 'status'=>$this->status, 'payload'=>$this->payload, 'mapping_errors'=>$this->mapping_errors, 'received_at'=>$this->received_at?->toIso8601String(), 'acknowledged_at'=>$this->acknowledged_at?->toIso8601String()];
    }
}
