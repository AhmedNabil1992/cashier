<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Security\Models\User;

class EnsureCanManageEdgeDevices
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User || (! $user->is_default && ! $user->can('manage_edge_devices'))) {
            return new JsonResponse(['message' => 'This action is unauthorized.'], 403);
        }

        return $next($request);
    }
}
