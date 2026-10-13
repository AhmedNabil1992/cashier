<?php

namespace Webkul\QrOrdering\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->getKey(),
            'profile_id'           => $this->profile_id,
            'slug'                 => $this->slug,
            'name'                 => $this->name,
            'mode'                 => $this->mode,
            'requires_table_token' => $this->requires_table_token,
            'is_active'            => $this->is_active,
            'settings'             => $this->settings ?? [],
        ];
    }
}
