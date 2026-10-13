<?php

namespace Webkul\Delivery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Webkul\Delivery\Actions\IngestExternalOrder;
use Webkul\Delivery\Http\Requests\AcknowledgeExternalOrderRequest;
use Webkul\Delivery\Http\Requests\IngestExternalOrderRequest;
use Webkul\Delivery\Http\Resources\ExternalOrderResource;
use Webkul\Delivery\Models\Channel;
use Webkul\Delivery\Models\ExternalOrder;

class ExternalOrderController extends Controller
{
    public function ingest(IngestExternalOrderRequest $request, Channel $channel, IngestExternalOrder $ingestExternalOrder): ExternalOrderResource
    {
        abort_unless($channel->is_active, 404);

        return new ExternalOrderResource($ingestExternalOrder->execute($channel, $request->validated()));
    }

    public function pending(): AnonymousResourceCollection
    {
        return ExternalOrderResource::collection(ExternalOrder::query()->where('status', 'pending_review')->orderBy('received_at')->paginate());
    }

    public function acknowledge(AcknowledgeExternalOrderRequest $request, ExternalOrder $externalOrder): ExternalOrderResource
    {
        if ($externalOrder->status !== 'pending_review') {
            return new ExternalOrderResource($externalOrder);
        }

        $externalOrder->update($request->validated() + ['acknowledged_at' => now()]);

        return new ExternalOrderResource($externalOrder->refresh());
    }
}
