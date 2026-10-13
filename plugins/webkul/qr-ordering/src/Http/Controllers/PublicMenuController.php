<?php

namespace Webkul\QrOrdering\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Webkul\QrOrdering\Actions\ResolveGuestSession;
use Webkul\QrOrdering\Models\Channel;

class PublicMenuController extends Controller
{
    public function __invoke(Request $request, Channel $channel, ResolveGuestSession $resolveGuestSession): JsonResponse
    {
        abort_unless($channel->is_active, 404);
        $session = $resolveGuestSession->execute($request, $channel);
        $publication = $channel->profile->publications()->latest('version')->firstOrFail();
        $payload = $publication->payload;
        $payload['menus'] = collect($payload['menus'] ?? [])
            ->filter(fn (array $menu): bool => in_array('qr', $menu['channels'] ?? [], true))
            ->values()
            ->all();

        return response()->json([
            'data' => [
                'channel' => [
                    'slug'     => $channel->slug,
                    'name'     => $channel->name,
                    'mode'     => $channel->mode,
                    'settings' => $channel->settings ?? [],
                ],
                'session' => $session ? [
                    'id'              => $session->public_id,
                    'table_reference' => $session->table_reference,
                    'expires_at'      => $session->expires_at->toIso8601String(),
                ] : null,
                'catalog' => [
                    'version'      => $publication->version,
                    'checksum'     => $publication->checksum,
                    'payload'      => $payload,
                    'published_at' => $publication->published_at?->toIso8601String(),
                ],
            ],
        ]);
    }
}
