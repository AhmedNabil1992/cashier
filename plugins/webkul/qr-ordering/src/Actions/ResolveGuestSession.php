<?php

namespace Webkul\QrOrdering\Actions;

use Illuminate\Http\Request;
use Webkul\QrOrdering\Models\Channel;
use Webkul\QrOrdering\Models\GuestSession;

class ResolveGuestSession
{
    public function execute(Request $request, Channel $channel): ?GuestSession
    {
        $token = $request->header('X-QR-Session-Token');

        if (! is_string($token) || $token === '') {
            abort_if($channel->requires_table_token, 401, 'A valid QR session token is required.');

            return null;
        }

        $session = $channel->guestSessions()->where('token_hash', hash('sha256', $token))->first();

        abort_unless($session?->isUsable(), 401, 'The QR session token is invalid or expired.');

        $session->forceFill(['last_seen_at' => now()])->save();

        return $session;
    }
}
