<?php

namespace App\Actions\Tenancy;

use App\Models\Tenant;

class RecordPluginPurchase
{
    public function __construct(private readonly SyncTenantPlugins $syncTenantPlugins) {}

    /** @param array<string, mixed> $attributes */
    public function execute(Tenant $tenant, array $attributes): array
    {
        $purchases = $tenant->plugin_purchases ?? [];

        abort_if(
            array_key_exists($attributes['purchase_id'], $purchases),
            409,
            'The purchase has already been recorded.',
        );

        $plugin = $this->syncTenantPlugins->execute($tenant, [$attributes['plugin']])[0];
        $purchases[$attributes['purchase_id']] = [
            'payment_reference' => $attributes['payment_reference'],
            'purchased_at'      => $attributes['purchased_at'],
            'entitlement'       => $attributes['entitlement'],
            'plugin_name'       => $attributes['plugin']['name'],
        ];
        $tenant->update(['plugin_purchases' => $purchases]);

        return $plugin;
    }
}
