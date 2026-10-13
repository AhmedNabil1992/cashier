<?php

namespace Webkul\Delivery\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id'=>$this->getKey(), 'company_id'=>$this->company_id, 'name'=>$this->name, 'phone'=>$this->phone, 'external_reference'=>$this->external_reference, 'status'=>$this->status, 'is_active'=>$this->is_active];
    }
}
