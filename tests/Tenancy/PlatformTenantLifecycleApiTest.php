<?php

declare(strict_types=1);

use App\Jobs\ProvisionTenantDatabase;
use App\Models\Tenant;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\TestResponse;
use Tests\TenancyTestCase;
use Webkul\PluginManager\Models\Plugin;

uses(TenancyTestCase::class);

function signedPlatformRequest(
    TenancyTestCase $test,
    string $method,
    string $uri,
    array $payload = [],
    ?string $idempotencyKey = null,
): TestResponse {
    $body = json_encode($payload, JSON_THROW_ON_ERROR);
    $timestamp = (string) now()->timestamp;
    $signature = hash_hmac(
        'sha256',
        implode("\n", [$timestamp, strtoupper($method), $uri, $body]),
        'platform-test-secret',
    );
    $headers = [
        'X-Platform-Timestamp' => $timestamp,
        'X-Platform-Signature' => $signature,
    ];

    if ($idempotencyKey) {
        $headers['Idempotency-Key'] = $idempotencyKey;
    }

    if (strtoupper($method) === 'GET') {
        return $test->withHeaders($headers)->getJson($uri);
    }

    return $test->withHeaders($headers)->json($method, $uri, $payload);
}

beforeEach(function (): void {
    config([
        'services.platform_api.secret'             => 'platform-test-secret',
        'services.platform_api.tenant_root_domain' => 'cashier.test',
    ]);
});

it('queues tenant database provisioning and returns immediately', function () {
    Queue::fake();

    $payload = [
        'platform_customer_id' => 'customer_queued',
        'subdomain'            => 'queued-cafe',
        'subscription'         => [
            'plan_key'      => 'restaurant-core',
            'billing_cycle' => 'monthly',
            'starts_at'     => '2026-09-23T00:00:00Z',
            'ends_at'       => '2026-10-22T23:59:59Z',
        ],
        'plugins' => [['name' => 'restaurant-pos']],
    ];

    $response = signedPlatformRequest($this, 'POST', '/api/platform/v1/tenants', $payload, 'create-customer-queued');

    $response->assertStatus(202)
        ->assertJsonPath('data.status', 'provisioning')
        ->assertJsonPath('data.domain', 'queued-cafe.cashier.test');

    $tenantId = $response->json('data.tenant_id');

    Queue::assertPushed(ProvisionTenantDatabase::class, fn (ProvisionTenantDatabase $job): bool => $job->tenantId === $tenantId
        && $job->plugins === [['name' => 'restaurant-pos']]);
});

it('provisions a tenant with core only and replays the same create request safely', function () {
    $payload = [
        'platform_customer_id' => 'customer_10025',
        'subdomain'            => 'cairo-cafe',
        'subscription'         => [
            'plan_key'      => 'restaurant-core',
            'billing_cycle' => 'yearly',
            'starts_at'     => '2026-09-23T00:00:00Z',
            'ends_at'       => '2027-09-22T23:59:59Z',
        ],
        'plugins' => [],
    ];

    $response = signedPlatformRequest($this, 'POST', '/api/platform/v1/tenants', $payload, 'create-customer-10025');

    $response->assertStatus(202)
        ->assertJsonPath('data.status', 'ready')
        ->assertJsonPath('data.domain', 'cairo-cafe.cashier.test');

    $tenantId = $response->json('data.tenant_id');
    $tenant = Tenant::query()->findOrFail($tenantId);

    expect($tenant->platform_customer_id)->toBe('customer_10025')
        ->and($tenant->subscription['plan_key'])->toBe('restaurant-core')
        ->and($tenant->domains()->value('domain'))->toBe('cairo-cafe.cashier.test');

    $tenant->run(fn () => expect(Plugin::query()->count())->toBe(0));

    signedPlatformRequest($this, 'GET', "/api/platform/v1/tenants/{$tenantId}/provisioning")
        ->assertOk()
        ->assertJsonPath('data.status', 'ready')
        ->assertJsonPath('data.domain', 'cairo-cafe.cashier.test');

    signedPlatformRequest($this, 'POST', '/api/platform/v1/tenants', $payload, 'create-customer-10025')
        ->assertStatus(202)
        ->assertHeader('Idempotency-Replayed', 'true')
        ->assertJsonPath('data.tenant_id', $tenantId);

    expect(Tenant::query()->count())->toBe(1);

    signedPlatformRequest($this, 'POST', '/api/platform/v1/tenants', $payload, 'different-create-key-10025')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('subdomain');
});

it('renews a tenant and records a purchased plugin without installing it', function () {
    $tenant = Tenant::query()->create([
        'id'                  => 'restaurant',
        'provisioning_status' => 'ready',
        'subscription'        => [],
        'plugin_purchases'    => [],
    ]);
    ProvisionTenantDatabase::dispatchSync($tenant->getTenantKey());

    $renewal = [
        'payment_reference' => 'pay_renewal_1',
        'plan_key'          => 'restaurant-pro',
        'billing_cycle'     => 'yearly',
        'starts_at'         => '2026-09-23T00:00:00Z',
        'ends_at'           => '2027-09-22T23:59:59Z',
        'plugins'           => [['name' => 'tables', 'latest_version' => '1.0.0']],
    ];

    signedPlatformRequest($this, 'POST', '/api/platform/v1/tenants/restaurant/renewals', $renewal, 'renew-restaurant-2026')
        ->assertOk()
        ->assertJsonPath('data.plan_key', 'restaurant-pro')
        ->assertJsonPath('data.plugins_synced', 1);

    $tenant->run(function (): void {
        $plugin = Plugin::query()->where('name', 'tables')->firstOrFail();
        expect($plugin->is_installed)->toBeFalse()->and($plugin->is_active)->toBeFalse();
        $plugin->update(['is_installed' => true, 'is_active' => true]);
    });

    $purchase = [
        'purchase_id'       => 'purchase_45812',
        'payment_reference' => 'pay_784900',
        'purchased_at'      => '2026-09-23T11:30:00Z',
        'entitlement'       => [
            'starts_at' => '2026-09-23T11:30:00Z',
            'ends_at'   => '2027-09-22T23:59:59Z',
        ],
        'plugin' => ['name' => 'tables', 'latest_version' => '1.1.0'],
    ];

    signedPlatformRequest($this, 'POST', '/api/platform/v1/tenants/restaurant/plugin-purchases', $purchase, 'purchase-45812-key')
        ->assertOk()
        ->assertJsonPath('data.purchase_id', 'purchase_45812')
        ->assertJsonPath('data.plugin_name', 'tables')
        ->assertJsonPath('data.available_for_installation', true);

    signedPlatformRequest($this, 'POST', '/api/platform/v1/tenants/restaurant/plugin-purchases', $purchase, 'purchase-45812-other-key')
        ->assertConflict();

    $tenant->refresh();
    expect($tenant->plugin_purchases['purchase_45812']['plugin_name'])->toBe('tables');

    $tenant->run(function (): void {
        $plugin = Plugin::query()->where('name', 'tables')->firstOrFail();
        expect($plugin->latest_version)->toBe('1.1.0')
            ->and($plugin->is_installed)->toBeTrue()
            ->and($plugin->is_active)->toBeTrue();
    });
});

it('rejects reuse of an idempotency key with different content', function () {
    $firstPayload = [
        'platform_customer_id' => 'customer_1',
        'subdomain'            => 'first-cafe',
        'subscription'         => [
            'plan_key'      => 'core',
            'billing_cycle' => 'monthly',
            'starts_at'     => '2026-09-23T00:00:00Z',
            'ends_at'       => '2026-10-22T23:59:59Z',
        ],
    ];

    signedPlatformRequest($this, 'POST', '/api/platform/v1/tenants', $firstPayload, 'same-create-key-123')->assertStatus(202);

    $secondPayload = $firstPayload;
    $secondPayload['subdomain'] = 'different-cafe';

    signedPlatformRequest($this, 'POST', '/api/platform/v1/tenants', $secondPayload, 'same-create-key-123')
        ->assertConflict();
});
