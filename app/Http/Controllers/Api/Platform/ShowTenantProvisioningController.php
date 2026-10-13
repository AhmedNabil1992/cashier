<?php

namespace App\Http\Controllers\Api\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

class ShowTenantProvisioningController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Tenant $tenant): JsonResponse
    {
        return response()->json([
            'data' => [
                'tenant_id'    => $tenant->getTenantKey(),
                'status'       => $tenant->provisioning_status,
                'domain'       => $tenant->domains()->value('domain'),
                'completed_at' => $tenant->provisioned_at,
            ],
        ]);
    }
}
