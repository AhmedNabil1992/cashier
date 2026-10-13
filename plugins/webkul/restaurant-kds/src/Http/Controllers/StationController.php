<?php

namespace Webkul\RestaurantKds\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Webkul\RestaurantKds\Actions\SaveStation;
use Webkul\RestaurantKds\Http\Requests\SaveStationRequest;
use Webkul\RestaurantKds\Http\Resources\StationResource;
use Webkul\RestaurantKds\Models\Station;

class StationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return StationResource::collection(
            Station::query()->with('routingRules')->orderBy('sort')->orderBy('name')->paginate()
        );
    }

    public function store(SaveStationRequest $request, SaveStation $saveStation): StationResource
    {
        return new StationResource($saveStation->execute($request->validated()));
    }

    public function show(Station $station): StationResource
    {
        return new StationResource($station->load('routingRules'));
    }

    public function update(SaveStationRequest $request, Station $station, SaveStation $saveStation): StationResource
    {
        return new StationResource($saveStation->execute($request->validated(), $station));
    }

    public function destroy(Station $station): Response|JsonResponse
    {
        if ($station->tickets()->exists()) {
            return response()->json([
                'message' => 'The station cannot be deleted while ticket projections reference it.',
            ], 409);
        }

        $station->delete();

        return response()->noContent();
    }
}
