<?php

namespace App\Http\Controllers\Api\Platform;

use App\Actions\Tenancy\SyncTenantPlugins;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\SyncTenantPluginsRequest;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

class SyncTenantPluginsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        SyncTenantPluginsRequest $request,
        Tenant $tenant,
        SyncTenantPlugins $syncTenantPlugins,
    ): JsonResponse {
        return response()->json([
            'tenant_id' => $tenant->getTenantKey(),
            'plugins'   => $syncTenantPlugins->execute($tenant, $request->validated('plugins')),
        ]);
    }
}
