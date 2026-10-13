<?php

namespace Webkul\RestaurantKds\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\RestaurantKds\Actions\SyncTicketProjections;
use Webkul\RestaurantKds\Http\Requests\SyncTicketsRequest;
use Webkul\RestaurantKds\Models\TicketProjection;

class TicketProjectionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            TicketProjection::query()->with('station')->orderByDesc('source_updated_at')->paginate()
        );
    }

    public function store(SyncTicketsRequest $request, SyncTicketProjections $sync): JsonResponse
    {
        return response()->json([
            'data' => $sync->execute($request->validated('tickets')),
        ]);
    }
}
