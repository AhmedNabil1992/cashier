<?php

namespace App\Http\Controllers\Api\Platform;

use App\Actions\Tenancy\RecordPluginPurchase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\RecordPluginPurchaseRequest;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

class RecordPluginPurchaseController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        RecordPluginPurchaseRequest $request,
        Tenant $tenant,
        RecordPluginPurchase $recordPluginPurchase,
    ): JsonResponse {
        $attributes = $request->validated();
        $plugin = $recordPluginPurchase->execute($tenant, $attributes);

        return response()->json([
            'data' => [
                'tenant_id'                  => $tenant->getTenantKey(),
                'purchase_id'                => $attributes['purchase_id'],
                'plugin_name'                => $plugin['name'],
                'entitlement_status'         => 'active',
                'available_for_installation' => true,
            ],
        ]);
    }
}
