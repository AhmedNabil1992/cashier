<?php

namespace Webkul\RestaurantPos\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogPublicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->getKey(),
            'profile_id'   => $this->profile_id,
            'version'      => $this->version,
            'checksum'     => $this->checksum,
            'payload'      => $this->payload,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
