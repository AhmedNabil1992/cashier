<?php

namespace App\Actions\Tenancy;

use App\Jobs\ProvisionTenantDatabase;
use App\Models\Tenant;
use Throwable;

class ProvisionTenant
{
    /** @param array<string, mixed> $attributes */
    public function execute(array $attributes): Tenant
    {
        $tenant = null;

        try {
            $tenant = Tenant::query()->create([
                'platform_customer_id' => $attributes['platform_customer_id'],
                'provisioning_status'  => 'provisioning',
                'subscription'         => $attributes['subscription'],
                'plugin_purchases'     => [],
            ]);

            $domain = $attributes['subdomain'].'.'.config('services.platform_api.tenant_root_domain');
            $tenant->domains()->create(['domain' => $domain]);

            ProvisionTenantDatabase::dispatch($tenant->getTenantKey(), $attributes['plugins'] ?? []);

            return $tenant->refresh();
        } catch (Throwable $exception) {
            if ($tenant?->exists) {
                $tenant->update(['provisioning_status' => 'failed']);
            }

            throw $exception;
        }
    }
}
