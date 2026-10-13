<?php

namespace App\Http\Controllers\Api\Platform;

use App\Actions\Tenancy\RenewTenantSubscription;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\RenewTenantSubscriptionRequest;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

class RenewTenantSubscriptionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        RenewTenantSubscriptionRequest $request,
        Tenant $tenant,
        RenewTenantSubscription $renewTenantSubscription,
    ): JsonResponse {
        $tenant = $renewTenantSubscription->execute($tenant, $request->validated());

        return response()->json([
            'data' => [
                'tenant_id'           => $tenant->getTenantKey(),
                'subscription_status' => 'active',
                'plan_key'            => $tenant->subscription['plan_key'],
                'ends_at'             => $tenant->subscription['ends_at'],
                'plugins_synced'      => count($request->validated('plugins', [])),
            ],
        ]);
    }
}
