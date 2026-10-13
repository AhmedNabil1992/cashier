<?php

namespace Webkul\Delivery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\Delivery\Actions\SyncTaskProjections;
use Webkul\Delivery\Http\Requests\SyncTasksRequest;
use Webkul\Delivery\Models\TaskProjection;

class TaskProjectionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TaskProjection::query()->orderByDesc('source_updated_at')->paginate());
    }

    public function store(SyncTasksRequest $request, SyncTaskProjections $sync): JsonResponse
    {
        return response()->json(['data'=>$sync->execute($request->validated('tasks'))]);
    }
}
