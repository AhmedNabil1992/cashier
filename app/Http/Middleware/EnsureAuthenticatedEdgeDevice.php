<?php

namespace App\Http\Middleware;

use App\Models\EdgeDevice;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticatedEdgeDevice
{
    public function handle(Request $request, Closure $next, ?string $ability = null): Response
    {
        $device = $request->user();

        if (! $device instanceof EdgeDevice || ! $device->isUsable()) {
            return new JsonResponse(['message' => 'This edge device is not authorized.'], 403);
        }

        if ($ability !== null && ! $device->tokenCan($ability)) {
            return new JsonResponse(['message' => 'The edge device token lacks the required ability.'], 403);
        }

        if ($device->last_seen_at === null || $device->last_seen_at->lt(now()->subMinutes(5))) {
            $device->forceFill(['last_seen_at' => now()])->saveQuietly();
        }

        return $next($request);
    }
}
