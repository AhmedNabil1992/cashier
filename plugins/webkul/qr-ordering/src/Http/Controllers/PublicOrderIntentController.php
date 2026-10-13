<?php

namespace Webkul\QrOrdering\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webkul\QrOrdering\Actions\ResolveGuestSession;
use Webkul\QrOrdering\Actions\SubmitOrderIntent;
use Webkul\QrOrdering\Http\Requests\CreateOrderIntentRequest;
use Webkul\QrOrdering\Http\Resources\OrderIntentResource;
use Webkul\QrOrdering\Models\Channel;
use Webkul\QrOrdering\Models\OrderIntent;

class PublicOrderIntentController extends Controller
{
    public function store(
        CreateOrderIntentRequest $request,
        Channel $channel,
        ResolveGuestSession $resolveGuestSession,
        SubmitOrderIntent $submitOrderIntent,
    ): OrderIntentResource {
        abort_unless($channel->is_active && $channel->mode === 'browse_and_order', 404);
        $session = $resolveGuestSession->execute($request, $channel);

        return new OrderIntentResource($submitOrderIntent->execute($channel, $session, $request->validated()));
    }

    public function show(Request $request, Channel $channel, OrderIntent $orderIntent, ResolveGuestSession $resolveGuestSession): OrderIntentResource
    {
        abort_unless((int) $orderIntent->channel_id === (int) $channel->getKey(), 404);
        $session = $resolveGuestSession->execute($request, $channel);

        if ($orderIntent->guest_session_id !== null) {
            abort_unless((int) $orderIntent->guest_session_id === (int) $session?->getKey(), 404);
        }

        return new OrderIntentResource($orderIntent);
    }
}
