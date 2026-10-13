<?php

namespace Webkul\RestaurantTables\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Webkul\RestaurantTables\Actions\SaveReservation;
use Webkul\RestaurantTables\Http\Requests\SaveReservationRequest;
use Webkul\RestaurantTables\Http\Resources\ReservationResource;
use Webkul\RestaurantTables\Models\Reservation;

class ReservationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ReservationResource::collection(Reservation::query()->orderBy('starts_at')->paginate());
    }

    public function store(SaveReservationRequest $request, SaveReservation $saveReservation): ReservationResource
    {
        return new ReservationResource($saveReservation->execute($request->validated()));
    }

    public function show(Reservation $reservation): ReservationResource
    {
        return new ReservationResource($reservation);
    }

    public function update(SaveReservationRequest $request, Reservation $reservation, SaveReservation $saveReservation): ReservationResource
    {
        return new ReservationResource($saveReservation->execute($request->validated(), $reservation));
    }

    public function destroy(Reservation $reservation): Response
    {
        abort_if(in_array($reservation->status, ['seated', 'completed'], true), 409, 'Operational reservation history cannot be deleted.');
        $reservation->delete();

        return response()->noContent();
    }
}
