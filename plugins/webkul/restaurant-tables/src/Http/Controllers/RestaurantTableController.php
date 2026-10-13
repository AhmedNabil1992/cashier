<?php

namespace Webkul\RestaurantTables\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Webkul\RestaurantTables\Http\Requests\SaveTableRequest;
use Webkul\RestaurantTables\Http\Resources\TableResource;
use Webkul\RestaurantTables\Models\Floor;
use Webkul\RestaurantTables\Models\RestaurantTable;

class RestaurantTableController extends Controller
{
    public function index(Floor $floor): AnonymousResourceCollection
    {
        return TableResource::collection($floor->tables()->orderBy('name')->paginate());
    }

    public function store(SaveTableRequest $request, Floor $floor): TableResource
    {
        return new TableResource($floor->tables()->create($request->validated() + ['locator' => (string) Str::uuid()]));
    }

    public function show(Floor $floor, RestaurantTable $table): TableResource
    {
        $this->ensureFloor($floor, $table);

        return new TableResource($table);
    }

    public function update(SaveTableRequest $request, Floor $floor, RestaurantTable $table): TableResource
    {
        $this->ensureFloor($floor, $table);
        $table->update($request->validated());

        return new TableResource($table->refresh());
    }

    public function destroy(Floor $floor, RestaurantTable $table): Response
    {
        $this->ensureFloor($floor, $table);
        abort_if($table->reservations()->exists(), 409, 'A table with reservation history cannot be deleted.');
        $table->delete();

        return response()->noContent();
    }

    private function ensureFloor(Floor $floor, RestaurantTable $table): void
    {
        abort_unless((int) $table->floor_id === (int) $floor->getKey(), 404);
    }
}
