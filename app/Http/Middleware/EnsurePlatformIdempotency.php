<?php

namespace App\Http\Middleware;

use App\Models\PlatformApiRequest;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformIdempotency
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('Idempotency-Key');

        if (! is_string($key) || ! preg_match('/^[A-Za-z0-9_-]{16,255}$/', $key)) {
            return response()->json(['message' => 'A valid Idempotency-Key header is required.'], 422);
        }

        $requestHash = hash('sha256', implode("\n", [
            strtoupper($request->method()),
            $request->getPathInfo(),
            $request->getContent(),
        ]));
        $storedRequest = PlatformApiRequest::query()->where('idempotency_key', $key)->first();

        if ($storedRequest) {
            if (! hash_equals($storedRequest->request_hash, $requestHash)) {
                return response()->json(['message' => 'The Idempotency-Key was already used for a different request.'], 409);
            }

            return response()->json($storedRequest->response_body, $storedRequest->response_status)
                ->header('Idempotency-Replayed', 'true');
        }

        $response = $next($request);

        if ($response instanceof JsonResponse && $response->isSuccessful()) {
            PlatformApiRequest::query()->create([
                'idempotency_key' => $key,
                'method'          => strtoupper($request->method()),
                'path'            => $request->getPathInfo(),
                'request_hash'    => $requestHash,
                'response_status' => $response->getStatusCode(),
                'response_body'   => $response->getData(true),
            ]);
        }

        return $response;
    }
}
