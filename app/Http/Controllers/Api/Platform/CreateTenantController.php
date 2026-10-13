<?php

namespace App\Http\Controllers\Api\Platform;

use App\Actions\Tenancy\ProvisionTenant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\CreateTenantRequest;
use Illuminate\Http\JsonResponse;

class CreateTenantController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateTenantRequest $request, ProvisionTenant $provisionTenant): JsonResponse
    {
        $tenant = $provisionTenant->execute($request->validated());

        return response()->json([
            'data' => [
                'tenant_id'  => $tenant->getTenantKey(),
                'status'     => $tenant->provisioning_status,
                'domain'     => $tenant->domains()->value('domain'),
                'status_url' => route('platform.tenants.provisioning.show', $tenant, false),
            ],
        ], 202);
    }
}
