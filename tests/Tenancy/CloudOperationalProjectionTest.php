<?php

declare(strict_types=1);

use App\Models\Tenant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Tests\TenancyTestCase;
use Webkul\Delivery\Actions\SyncTaskProjections;
use Webkul\Delivery\Models\TaskProjection;
use Webkul\RestaurantKds\Actions\SyncTicketProjections;
use Webkul\RestaurantKds\Models\TicketProjection;
use Webkul\RestaurantTables\Actions\SyncTableSessionProjections;
use Webkul\RestaurantTables\Models\TableSessionProjection;

uses(TenancyTestCase::class);

it('keeps the newest edge version in table delivery and kds cloud projections', function () {
    $tenant = Tenant::query()->create(['id' => 'restaurant']);
    app()->call([new CreateDatabase($tenant), 'handle']);

    $tenant->run(function (): void {
        Schema::create('restaurant_tables', function (Blueprint $table): void {
            $table->id();
        });
        Schema::create('restaurant_table_session_projections', function (Blueprint $table): void {
            $table->id();
            $table->uuid('edge_session_id')->unique();
            $table->unsignedBigInteger('table_id');
            $table->uuid('edge_order_id')->nullable();
            $table->string('waiter_reference')->nullable();
            $table->unsignedSmallInteger('guest_count');
            $table->string('status');
            $table->unsignedBigInteger('version');
            $table->json('payload')->nullable();
            $table->timestamp('source_updated_at');
            $table->timestamp('last_synced_at');
            $table->timestamps();
        });
        Schema::create('delivery_task_projections', function (Blueprint $table): void {
            $table->id();
            $table->uuid('edge_task_id')->unique();
            $table->uuid('edge_order_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('status');
            $table->unsignedBigInteger('version');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('source_updated_at');
            $table->timestamp('last_synced_at');
            $table->timestamps();
        });
        Schema::create('restaurant_pos_profiles', function (Blueprint $table): void {
            $table->id();
        });
        Schema::create('restaurant_kds_stations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('profile_id');
        });
        Schema::create('restaurant_kds_ticket_projections', function (Blueprint $table): void {
            $table->id();
            $table->uuid('edge_ticket_id')->unique();
            $table->uuid('edge_order_id');
            $table->unsignedBigInteger('station_id');
            $table->string('status');
            $table->unsignedSmallInteger('priority');
            $table->unsignedBigInteger('version');
            $table->json('payload')->nullable();
            $table->timestamp('source_created_at');
            $table->timestamp('source_updated_at');
            $table->timestamp('last_synced_at');
            $table->timestamps();
        });

        $tableId = Schema::getConnection()->table('restaurant_tables')->insertGetId([]);
        $profileId = Schema::getConnection()->table('restaurant_pos_profiles')->insertGetId([]);
        $stationId = Schema::getConnection()->table('restaurant_kds_stations')->insertGetId(['profile_id' => $profileId]);
        $sessionId = '018f47f0-92a2-7bc1-b37a-57dc00f72c11';
        $taskId = '018f47f0-92a2-7bc1-b37a-57dc00f72c12';
        $orderId = '018f47f0-92a2-7bc1-b37a-57dc00f72c13';
        $ticketId = '018f47f0-92a2-7bc1-b37a-57dc00f72c14';

        app(SyncTableSessionProjections::class)->execute([[
            'edge_session_id' => $sessionId, 'table_id' => $tableId, 'guest_count' => 2,
            'status'          => 'ordering', 'version' => 2, 'source_updated_at' => now(),
        ]]);
        $staleTable = app(SyncTableSessionProjections::class)->execute([[
            'edge_session_id' => $sessionId, 'table_id' => $tableId, 'guest_count' => 2,
            'status'          => 'seated', 'version' => 1, 'source_updated_at' => now()->subMinute(),
        ]]);
        app(SyncTaskProjections::class)->execute([[
            'edge_task_id' => $taskId, 'edge_order_id' => $orderId,
            'status'       => 'out_for_delivery', 'version' => 3, 'source_updated_at' => now(),
        ]]);
        $staleDelivery = app(SyncTaskProjections::class)->execute([[
            'edge_task_id' => $taskId, 'edge_order_id' => $orderId,
            'status'       => 'assigned', 'version' => 2, 'source_updated_at' => now()->subMinute(),
        ]]);
        app(SyncTicketProjections::class)->execute([[
            'edge_ticket_id'    => $ticketId,
            'edge_order_id'     => $orderId,
            'station_id'        => $stationId,
            'status'            => 'ready',
            'priority'          => 10,
            'version'           => 4,
            'source_created_at' => now()->subMinutes(5),
            'source_updated_at' => now(),
        ]]);
        $staleKds = app(SyncTicketProjections::class)->execute([[
            'edge_ticket_id'    => $ticketId,
            'edge_order_id'     => $orderId,
            'station_id'        => $stationId,
            'status'            => 'preparing',
            'priority'          => 10,
            'version'           => 3,
            'source_created_at' => now()->subMinutes(5),
            'source_updated_at' => now()->subMinute(),
        ]]);

        expect(TableSessionProjection::query()->first()->status)->toBe('ordering')
            ->and($staleTable['stale'])->toBe(1)
            ->and(TaskProjection::query()->first()->status)->toBe('out_for_delivery')
            ->and($staleDelivery['stale'])->toBe(1)
            ->and(TicketProjection::query()->first()->status)->toBe('ready')
            ->and($staleKds['stale'])->toBe(1);
    });
});
