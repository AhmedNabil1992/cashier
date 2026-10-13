<?php

namespace App\Http\Controllers\Api\Edge;

use App\Actions\Edge\ReceiveSyncEvents;
use App\Http\Controllers\Controller;
use App\Http\Requests\Edge\ReceiveSyncEventsRequest;
use App\Models\EdgeDevice;
use Illuminate\Http\JsonResponse;

class SyncEventController extends Controller
{
    public function __invoke(ReceiveSyncEventsRequest $request, ReceiveSyncEvents $receiver): JsonResponse
    {
        /** @var EdgeDevice $device */
        $device = $request->user();

        return response()->json($receiver->execute($device, $request->validated('events')), 202);
    }
}
