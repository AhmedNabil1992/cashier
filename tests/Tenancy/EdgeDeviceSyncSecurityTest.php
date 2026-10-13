<?php

declare(strict_types=1);

use App\Jobs\ProvisionTenantDatabase;
use App\Models\EdgeDevice;
use App\Models\SyncInboxEvent;
use App\Models\Tenant;
use App\Models\User;
use Tests\TenancyTestCase;

uses(TenancyTestCase::class);

it('accepts idempotent sync events only from an active edge device with the required ability', function (): void {
    $tenant = Tenant::query()->create(['id' => 'edge-security']);
    ProvisionTenantDatabase::dispatchSync($tenant->getTenantKey());
    $tenant->domains()->create(['domain' => 'edge-security.example.test']);

    [$token, $deviceId] = $tenant->run(function (): array {
        $device = EdgeDevice::query()->create([
            'name'      => 'Main POS',
            'type'      => 'pos',
            'status'    => 'active',
            'abilities' => ['edge:sync'],
        ]);

        return [$device->createToken('edge-test', ['edge:sync'])->plainTextToken, $device->getKey()];
    });

    $event = [
        'event_id'          => '019dd35e-c3f4-7000-8000-000000000001',
        'aggregate_type'    => 'sale_order',
        'aggregate_id'      => 'order-1001',
        'aggregate_version' => 1,
        'event_type'        => 'sale_order.closed',
        'schema_version'    => 1,
        'occurred_at'       => '2026-09-25T12:00:00Z',
        'payload'           => ['total' => 125.50, 'currency' => 'EGP'],
    ];
    $headers = ['Authorization' => 'Bearer '.$token];
    $url = 'http://edge-security.example.test/api/v1/edge/sync/events';

    $this->postJson($url, ['events' => [$event]], $headers)
        ->assertAccepted()
        ->assertJson(['accepted' => 1, 'duplicates' => 0]);

    $this->postJson($url, ['events' => [$event]], $headers)
        ->assertAccepted()
        ->assertJson(['accepted' => 0, 'duplicates' => 1]);

    $tenant->run(function () use ($deviceId): void {
        expect(SyncInboxEvent::query()->count())->toBe(1)
            ->and(SyncInboxEvent::query()->first()->source_edge_device_id)->toBe($deviceId);
    });

    $event['payload']['total'] = 130;
    $this->postJson($url, ['events' => [$event]], $headers)->assertConflict();
});

it('rejects user tokens, missing abilities, expired devices, and revoked devices', function (): void {
    $tenant = Tenant::query()->create(['id' => 'edge-denials']);
    ProvisionTenantDatabase::dispatchSync($tenant->getTenantKey());
    $tenant->domains()->create(['domain' => 'edge-denials.example.test']);

    [$userToken, $limitedToken, $expiredToken, $revokedToken] = $tenant->run(function (): array {
        $user = User::query()->create([
            'name'     => 'Tenant User',
            'email'    => 'user@example.test',
            'password' => 'secret',
        ]);
        $limited = EdgeDevice::query()->create([
            'name' => 'Limited', 'type' => 'pos', 'status' => 'active', 'abilities' => ['restaurant-kds:sync'],
        ]);
        $expired = EdgeDevice::query()->create([
            'name' => 'Expired', 'type' => 'pos', 'status' => 'active', 'abilities' => ['edge:sync'], 'expires_at' => now()->subMinute(),
        ]);
        $revoked = EdgeDevice::query()->create([
            'name' => 'Revoked', 'type' => 'pos', 'status' => 'revoked', 'abilities' => ['edge:sync'], 'revoked_at' => now(),
        ]);

        return [
            $user->createToken('user')->plainTextToken,
            $limited->createToken('limited', ['restaurant-kds:sync'])->plainTextToken,
            $expired->createToken('expired', ['edge:sync'])->plainTextToken,
            $revoked->createToken('revoked', ['edge:sync'])->plainTextToken,
        ];
    });

    $url = 'http://edge-denials.example.test/api/v1/edge/sync/events';
    $payload = ['events' => [[
        'event_id'       => '019dd35e-c3f4-7000-8000-000000000002', 'aggregate_type' => 'sale_order',
        'aggregate_id'   => 'order-1002', 'aggregate_version' => 1, 'event_type' => 'sale_order.closed',
        'schema_version' => 1, 'occurred_at' => '2026-09-25T12:00:00Z', 'payload' => ['total' => 10],
    ]]];

    foreach ([$userToken, $limitedToken, $expiredToken, $revokedToken] as $token) {
        $this->postJson($url, $payload, ['Authorization' => 'Bearer '.$token])->assertForbidden();
    }
});
