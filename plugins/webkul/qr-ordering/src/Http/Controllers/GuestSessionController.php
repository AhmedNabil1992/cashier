<?php

namespace Webkul\QrOrdering\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Webkul\QrOrdering\Http\Requests\CreateGuestSessionRequest;
use Webkul\QrOrdering\Models\Channel;
use Webkul\QrOrdering\Models\GuestSession;

class GuestSessionController extends Controller
{
    public function store(CreateGuestSessionRequest $request, Channel $channel): JsonResponse
    {
        $plainToken = Str::random(64);
        $session = $channel->guestSessions()->create([
            'public_id'       => (string) Str::uuid(),
            'token_hash'      => hash('sha256', $plainToken),
            'table_reference' => $request->validated('table_reference'),
            'expires_at'      => now()->addMinutes($request->integer('expires_in_minutes', 120)),
            'metadata'        => $request->validated('metadata'),
        ]);

        return response()->json([
            'data' => [
                'id'              => $session->public_id,
                'token'           => $plainToken,
                'table_reference' => $session->table_reference,
                'expires_at'      => $session->expires_at->toIso8601String(),
            ],
        ], 201);
    }

    public function destroy(Channel $channel, GuestSession $guestSession): JsonResponse
    {
        abort_unless((int) $guestSession->channel_id === (int) $channel->getKey(), 404);
        $guestSession->forceFill(['revoked_at' => now()])->save();

        return response()->json(['data' => ['revoked' => true]]);
    }
}
