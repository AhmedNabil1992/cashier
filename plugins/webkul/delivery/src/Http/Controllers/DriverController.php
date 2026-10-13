<?php

namespace Webkul\Delivery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Webkul\Delivery\Http\Requests\SaveDriverRequest;
use Webkul\Delivery\Http\Resources\DriverResource;
use Webkul\Delivery\Models\Driver;

class DriverController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return DriverResource::collection(Driver::query()->orderBy('name')->paginate());
    }

    public function store(SaveDriverRequest $request): DriverResource
    {
        return new DriverResource(Driver::query()->create($request->validated()));
    }

    public function show(Driver $driver): DriverResource
    {
        return new DriverResource($driver);
    }

    public function update(SaveDriverRequest $request, Driver $driver): DriverResource
    {
        $driver->update($request->validated());

        return new DriverResource($driver->refresh());
    }

    public function destroy(Driver $driver): Response
    {
        abort_if($driver->status === 'assigned', 409, 'An assigned driver cannot be deleted.');
        $driver->delete();

        return response()->noContent();
    }
}
