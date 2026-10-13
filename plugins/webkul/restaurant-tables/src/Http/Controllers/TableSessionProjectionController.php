<?php

namespace Webkul\RestaurantTables\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\RestaurantTables\Actions\SyncTableSessionProjections;
use Webkul\RestaurantTables\Http\Requests\SyncTableSessionsRequest;
use Webkul\RestaurantTables\Models\TableSessionProjection;

class TableSessionProjectionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TableSessionProjection::query()->with('table')->orderByDesc('source_updated_at')->paginate());
    }

    public function store(SyncTableSessionsRequest $request, SyncTableSessionProjections $sync): JsonResponse
    {
        return response()->json(['data'=>$sync->execute($request->validated('sessions'))]);
    }
}
