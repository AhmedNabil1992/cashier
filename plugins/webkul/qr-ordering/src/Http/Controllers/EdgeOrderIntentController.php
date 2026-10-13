<?php

namespace Webkul\QrOrdering\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Webkul\QrOrdering\Actions\AcknowledgeOrderIntent;
use Webkul\QrOrdering\Http\Requests\AcknowledgeOrderIntentRequest;
use Webkul\QrOrdering\Http\Resources\OrderIntentResource;
use Webkul\QrOrdering\Models\OrderIntent;

class EdgeOrderIntentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return OrderIntentResource::collection(
            OrderIntent::query()->where('status', 'pending_edge')->orderBy('submitted_at')->paginate()
        );
    }

    public function update(
        AcknowledgeOrderIntentRequest $request,
        OrderIntent $orderIntent,
        AcknowledgeOrderIntent $acknowledgeOrderIntent,
    ): JsonResponse {
        $result = $acknowledgeOrderIntent->execute($orderIntent, $request->validated());

        return response()->json([
            'data' => [
                'applied'      => $result['applied'],
                'order_intent' => (new OrderIntentResource($result['order_intent']))->resolve($request),
            ],
        ]);
    }
}
