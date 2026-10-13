<?php

namespace Webkul\Delivery\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id'=>$this->getKey(), 'company_id'=>$this->company_id, 'name'=>$this->name, 'geometry'=>$this->geometry, 'delivery_fee'=>$this->delivery_fee, 'minimum_order'=>$this->minimum_order, 'is_active'=>$this->is_active];
    }
}
