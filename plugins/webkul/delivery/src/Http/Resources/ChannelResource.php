<?php

namespace Webkul\Delivery\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id'=>$this->getKey(), 'company_id'=>$this->company_id, 'provider'=>$this->provider, 'external_store_id'=>$this->external_store_id, 'configuration'=>$this->configuration, 'is_active'=>$this->is_active];
    }
}
