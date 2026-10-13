<?php

namespace Webkul\QrOrdering\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderIntentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->public_id,
            'channel_id'        => $this->channel_id,
            'status'            => $this->status,
            'catalog_version'   => $this->catalog_version,
            'total'             => $this->total,
            'payload'           => $this->payload,
            'edge_order_id'     => $this->edge_order_id,
            'version'           => $this->version,
            'rejection_reason'  => $this->rejection_reason,
            'submitted_at'      => $this->submitted_at?->toIso8601String(),
            'acknowledged_at'   => $this->acknowledged_at?->toIso8601String(),
        ];
    }
}
