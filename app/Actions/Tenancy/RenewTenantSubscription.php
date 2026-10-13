<?php

namespace App\Actions\Tenancy;

use App\Models\Tenant;

class RenewTenantSubscription
{
    public function __construct(private readonly SyncTenantPlugins $syncTenantPlugins) {}

    /** @param array<string, mixed> $attributes */
    public function execute(Tenant $tenant, array $attributes): Tenant
    {
        $plugins = $attributes['plugins'] ?? [];

        if ($plugins) {
            $this->syncTenantPlugins->execute($tenant, $plugins);
        }

        $tenant->update([
            'subscription' => [
                'plan_key'          => $attributes['plan_key'],
                'billing_cycle'     => $attributes['billing_cycle'],
                'starts_at'         => $attributes['starts_at'],
                'ends_at'           => $attributes['ends_at'],
                'payment_reference' => $attributes['payment_reference'],
                'renewed_at'        => now()->toIso8601String(),
            ],
        ]);

        return $tenant->refresh();
    }
}
