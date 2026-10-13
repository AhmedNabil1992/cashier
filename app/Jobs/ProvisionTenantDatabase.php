<?php

namespace App\Jobs;

use App\Actions\Tenancy\SyncTenantPlugins;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;
use Throwable;

class ProvisionTenantDatabase implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 1500;

    /** @param array<int, array<string, mixed>> $plugins */
    public function __construct(
        public readonly string $tenantId,
        public readonly array $plugins = [],
    ) {}

    public function handle(SyncTenantPlugins $syncTenantPlugins): void
    {
        $tenant = Tenant::query()->findOrFail($this->tenantId);
        $tenantDatabase = $tenant->database();

        if (! $tenantDatabase->manager()->databaseExists($tenantDatabase->getName())) {
            app()->call([new CreateDatabase($tenant), 'handle']);
        }

        app()->call([new MigrateDatabase($tenant), 'handle']);

        if ($this->plugins !== []) {
            $syncTenantPlugins->execute($tenant, $this->plugins);
        }

        $tenant->update([
            'provisioning_status' => 'ready',
            'provisioned_at'      => now()->toIso8601String(),
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        Tenant::query()->whereKey($this->tenantId)->update([
            'provisioning_status' => 'failed',
        ]);
    }
}
