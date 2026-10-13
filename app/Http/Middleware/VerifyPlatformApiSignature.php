<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPlatformApiSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('services.platform_api.secret');
        $timestamp = $request->header('X-Platform-Timestamp');
        $signature = $request->header('X-Platform-Signature');
        $ttl = (int) config('services.platform_api.signature_ttl_seconds', 300);

        if ($secret === '' || ! ctype_digit((string) $timestamp) || ! is_string($signature)) {
            return response()->json(['message' => 'Invalid platform signature.'], 401);
        }

        if (abs(now()->timestamp - (int) $timestamp) > $ttl) {
            return response()->json(['message' => 'Platform signature has expired.'], 401);
        }

        $signedPayload = implode("\n", [
            $timestamp,
            strtoupper($request->method()),
            $request->getPathInfo(),
            $request->getContent(),
        ]);

        $expectedSignature = hash_hmac('sha256', $signedPayload, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            return response()->json(['message' => 'Invalid platform signature.'], 401);
        }

        return $next($request);
    }
}
