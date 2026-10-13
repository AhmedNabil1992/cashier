<?php

declare(strict_types=1);

use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;
use Tests\TenancyTestCase;
use Webkul\PluginManager\Models\Plugin;

uses(TenancyTestCase::class);

it('syncs purchased plugins into only the requested tenant database', function () {
    config(['services.platform_api.secret' => 'platform-test-secret']);

    $tenant = Tenant::create(['id' => 'restaurant']);
    $uri = '/api/platform/v1/tenants/restaurant/plugins';
    $payload = [
        'plugins' => [
            [
                'name'           => 'website',
                'author'         => 'Webkul',
                'latest_version' => '1.0.0',
                'license'        => 'MIT',
                'sort'           => 10,
            ],
        ],
    ];

    $timestamp = (string) now()->timestamp;
    $body = json_encode($payload, JSON_THROW_ON_ERROR);
    $signature = hash_hmac('sha256', implode("\n", [$timestamp, 'POST', $uri, $body]), 'platform-test-secret');

    $this->withHeaders([
        'X-Platform-Timestamp' => $timestamp,
        'X-Platform-Signature' => $signature,
    ])->postJson($uri, $payload)
        ->assertOk()
        ->assertJsonPath('tenant_id', 'restaurant')
        ->assertJsonPath('plugins.0.name', 'website')
        ->assertJsonPath('plugins.0.created', true)
        ->assertJsonPath('plugins.0.is_installed', false)
        ->assertJsonPath('plugins.0.is_active', false);

    $tenant->run(function (): void {
        $plugin = Plugin::query()->where('name', 'website')->firstOrFail();

        expect($plugin->is_installed)->toBeFalse()
            ->and($plugin->is_active)->toBeFalse();

        $plugin->update([
            'is_installed' => true,
            'is_active'    => true,
        ]);
    });

    $payload['plugins'][0]['latest_version'] = '1.1.0';
    $body = json_encode($payload, JSON_THROW_ON_ERROR);
    $signature = hash_hmac('sha256', implode("\n", [$timestamp, 'POST', $uri, $body]), 'platform-test-secret');

    $this->withHeaders([
        'X-Platform-Timestamp' => $timestamp,
        'X-Platform-Signature' => $signature,
    ])->postJson($uri, $payload)->assertOk();

    $tenant->run(function (): void {
        $plugin = Plugin::query()->where('name', 'website')->firstOrFail();

        expect($plugin->latest_version)->toBe('1.1.0')
            ->and($plugin->is_installed)->toBeTrue()
            ->and($plugin->is_active)->toBeTrue();
    });

    tenancy()->central(fn () => expect(Schema::hasTable('plugins'))->toBeFalse());
});

it('rejects plugin sync requests with an invalid platform signature', function () {
    config(['services.platform_api.secret' => 'platform-test-secret']);

    $tenant = Tenant::create(['id' => 'restaurant']);

    $this->withHeaders([
        'X-Platform-Timestamp' => (string) now()->timestamp,
        'X-Platform-Signature' => 'invalid',
    ])->postJson('/api/platform/v1/tenants/restaurant/plugins', [
        'plugins' => [['name' => 'website']],
    ])->assertUnauthorized();

    $tenant->run(fn () => expect(Plugin::query()->count())->toBe(0));
});
