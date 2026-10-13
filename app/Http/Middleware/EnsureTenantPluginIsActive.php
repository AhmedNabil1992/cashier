<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Webkul\PluginManager\Models\Plugin;

class EnsureTenantPluginIsActive
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next, string $pluginName): Response
    {
        if (! tenancy()->initialized) {
            abort(404);
        }

        $isAvailable = Plugin::query()
            ->where('name', $pluginName)
            ->where('is_installed', true)
            ->where('is_active', true)
            ->exists();

        abort_unless($isAvailable, 404);

        return $next($request);
    }
}
