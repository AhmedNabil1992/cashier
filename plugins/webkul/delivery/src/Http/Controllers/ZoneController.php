<?php

namespace Webkul\Delivery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Webkul\Delivery\Http\Requests\SaveZoneRequest;
use Webkul\Delivery\Http\Resources\ZoneResource;
use Webkul\Delivery\Models\Zone;

class ZoneController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ZoneResource::collection(Zone::query()->orderBy('name')->paginate());
    }

    public function store(SaveZoneRequest $request): ZoneResource
    {
        return new ZoneResource(Zone::query()->create($request->validated()));
    }

    public function show(Zone $zone): ZoneResource
    {
        return new ZoneResource($zone);
    }

    public function update(SaveZoneRequest $request, Zone $zone): ZoneResource
    {
        $zone->update($request->validated());

        return new ZoneResource($zone->refresh());
    }

    public function destroy(Zone $zone): Response
    {
        $zone->delete();

        return response()->noContent();
    }
}
