<?php

namespace Webkul\RestaurantTables\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->uuid, 'company_id' => $this->company_id, 'table_id' => $this->table_id, 'guest_name' => $this->guest_name, 'guest_phone' => $this->guest_phone, 'party_size' => $this->party_size, 'starts_at' => $this->starts_at?->toIso8601String(), 'ends_at' => $this->ends_at?->toIso8601String(), 'status' => $this->status, 'deposit_amount' => $this->deposit_amount, 'notes' => $this->notes];
    }
}
