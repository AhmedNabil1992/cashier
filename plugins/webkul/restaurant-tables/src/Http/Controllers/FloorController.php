<?php

namespace Webkul\RestaurantTables\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Webkul\RestaurantTables\Http\Requests\SaveFloorRequest;
use Webkul\RestaurantTables\Http\Resources\FloorResource;
use Webkul\RestaurantTables\Models\Floor;

class FloorController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return FloorResource::collection(Floor::query()->with('tables')->orderBy('sort')->paginate());
    }

    public function store(SaveFloorRequest $request): FloorResource
    {
        return new FloorResource(Floor::query()->create($request->validated())->load('tables'));
    }

    public function show(Floor $floor): FloorResource
    {
        return new FloorResource($floor->load('tables'));
    }

    public function update(SaveFloorRequest $request, Floor $floor): FloorResource
    {
        $floor->update($request->validated());

        return new FloorResource($floor->refresh()->load('tables'));
    }

    public function destroy(Floor $floor): Response
    {
        abort_if($floor->tables()->exists(), 409, 'A floor with tables cannot be deleted.');
        $floor->delete();

        return response()->noContent();
    }
}
