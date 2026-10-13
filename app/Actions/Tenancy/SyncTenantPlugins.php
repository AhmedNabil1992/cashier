<?php

namespace App\Actions\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Models\Plugin;

class SyncTenantPlugins
{
    /**
     * @param  array<int, array<string, mixed>>  $plugins
     * @return array<int, array<string, mixed>>
     */
    public function execute(Tenant $tenant, array $plugins): array
    {
        return $tenant->run(function () use ($plugins): array {
            return DB::transaction(function () use ($plugins): array {
                $syncedPlugins = [];

                foreach ($plugins as $attributes) {
                    $plugin = Plugin::query()->firstOrNew(['name' => $attributes['name']]);
                    $wasCreated = ! $plugin->exists;

                    $plugin->fill($attributes);
                    $plugin->save();

                    $syncedPlugins[] = [
                        'name'         => $plugin->name,
                        'created'      => $wasCreated,
                        'is_installed' => (bool) $plugin->is_installed,
                        'is_active'    => (bool) $plugin->is_active,
                    ];
                }

                return $syncedPlugins;
            });
        });
    }
}
