<?php

declare(strict_types=1);

use App\Jobs\ProvisionTenantDatabase;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TenancyTestCase;
use Webkul\PluginManager\PackageServiceProvider;

uses(TenancyTestCase::class);

it('stores each tenant users in a separate database', function () {
    $firstTenant = Tenant::create(['id' => 'first']);
    $secondTenant = Tenant::create(['id' => 'second']);
    ProvisionTenantDatabase::dispatchSync($firstTenant->getTenantKey());
    ProvisionTenantDatabase::dispatchSync($secondTenant->getTenantKey());

    $firstTenant->run(function (): void {
        expect(Schema::hasTable('plugins'))->toBeTrue();

        DB::table('users')->insert([
            'name'     => 'First tenant user',
            'email'    => 'owner@example.com',
            'password' => 'secret',
        ]);

        expect(DB::table('users')->pluck('name')->all())
            ->toBe(['First tenant user']);
    });

    $secondTenant->run(function (): void {
        expect(DB::table('users')->count())->toBe(0);

        DB::table('users')->insert([
            'name'     => 'Second tenant user',
            'email'    => 'owner@example.com',
            'password' => 'secret',
        ]);

        expect(DB::table('users')->pluck('name')->all())
            ->toBe(['Second tenant user']);
    });

    $firstTenant->run(function (): void {
        expect(DB::table('users')->pluck('name')->all())
            ->toBe(['First tenant user']);
    });

    expect($firstTenant->database()->getName())
        ->not->toBe($secondTenant->database()->getName());

    $firstTenantDatabase = database_path($firstTenant->database()->getName());
    $firstTenant->delete();

    expect($firstTenantDatabase)->toBeFile();
});

it('resolves tenant routes by domain and rejects central domains', function () {
    $tenant = Tenant::create(['id' => 'restaurant']);
    $tenant->domains()->create(['domain' => 'restaurant.example.test']);

    $this->get('http://restaurant.example.test/tenant/health')
        ->assertOk()
        ->assertExactJson([
            'status'    => 'ok',
            'tenant_id' => 'restaurant',
        ]);

    $this->get('http://localhost/tenant/health')->assertNotFound();
});

it('keeps every declared plugin migration in its tenant directory', function () {
    foreach (array_keys(app()->getLoadedProviders()) as $providerClass) {
        if (! is_subclass_of($providerClass, PackageServiceProvider::class)) {
            continue;
        }

        $provider = app()->getProvider($providerClass);
        $package = Closure::bind(fn () => $this->package, $provider, $providerClass)();

        foreach ($package->migrationFileNames as $migration) {
            $path = $package->basePath("../database/migrations/tenant/{$migration}.php");

            expect(file_exists($path))->toBeTrue("{$providerClass}: {$path}");
        }
    }

    expect(collect(glob(database_path('migrations/*.php')))->map(fn (string $path): string => basename($path))->sort()->values()->all())
        ->toBe([
            '2019_09_15_000010_create_tenants_table.php',
            '2019_09_15_000020_create_domains_table.php',
            '2026_09_23_061706_create_platform_api_requests_table.php',
        ]);
});

it('does not register tenant plugin migrations on the central migrator', function () {
    $migrationPaths = collect(app('migrator')->paths())
        ->map(fn (string $path): string => str_replace('\\', '/', $path));

    expect($migrationPaths)
        ->not->toContain(str_replace('\\', '/', base_path('plugins/webkul/security/database/settings')))
        ->not->toContain(str_replace('\\', '/', base_path('plugins/webkul/support/database/settings')));
});

it('runs core setting migrations inside tenant databases', function () {
    $tenantMigrationPaths = collect(config('tenancy.migration_parameters.--path'))
        ->map(fn (string $path): string => str_replace('\\', '/', $path));

    expect($tenantMigrationPaths)
        ->toContain(str_replace('\\', '/', base_path('plugins/webkul/security/database/settings')))
        ->toContain(str_replace('\\', '/', base_path('plugins/webkul/support/database/settings')));
});
