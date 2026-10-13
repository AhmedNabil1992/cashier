<?php

declare(strict_types=1);

use App\Models\Tenant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TenancyTestCase;
use Webkul\Delivery\Actions\IngestExternalOrder;
use Webkul\Delivery\Models\Channel;
use Webkul\RestaurantReports\Services\RestaurantOperationalReport;
use Webkul\RestaurantTables\Actions\SaveReservation;

uses(TenancyTestCase::class);

it('protects reservations and external orders and aggregates available restaurant sources', function () {
    $tenant = Tenant::query()->create(['id' => 'restaurant-cloud-completion']);
    app()->call([new CreateDatabase($tenant), 'handle']);

    $tenant->run(function (): void {
        Schema::create('restaurant_reservations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('table_id')->nullable();
            $table->string('guest_name');
            $table->string('guest_phone')->nullable();
            $table->unsignedSmallInteger('party_size');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status');
            $table->decimal('deposit_amount', 15, 4);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('delivery_channels', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('provider');
            $table->string('external_store_id');
            $table->text('configuration')->nullable();
            $table->boolean('is_active');
            $table->timestamps();
        });
        Schema::create('delivery_external_orders', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('channel_id');
            $table->string('external_order_id');
            $table->uuid('edge_order_id')->nullable();
            $table->string('status');
            $table->json('payload');
            $table->json('mapping_errors')->nullable();
            $table->timestamp('received_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
        Schema::create('qr_ordering_order_intents', function (Blueprint $table): void {
            $table->id();
            $table->string('status');
            $table->decimal('total', 15, 4);
            $table->timestamp('submitted_at');
        });

        $reservation = [
            'company_id' => 1, 'table_id' => 10, 'guest_name' => 'Ahmed', 'party_size' => 4,
            'starts_at'  => now()->addHour(), 'ends_at' => now()->addHours(2), 'status' => 'confirmed', 'deposit_amount' => 100,
        ];
        app(SaveReservation::class)->execute($reservation);

        expect(fn () => app(SaveReservation::class)->execute($reservation))->toThrow(ValidationException::class);

        $channel = Channel::query()->create(['company_id' => 1, 'provider' => 'partner', 'external_store_id' => 'store-1', 'configuration' => ['secret' => 'hidden'], 'is_active' => true]);
        $rawConfiguration = DB::table('delivery_channels')->where('id', $channel->getKey())->value('configuration');
        $payload = ['external_order_id' => 'partner-100', 'payload' => ['total' => 250]];
        $first = app(IngestExternalOrder::class)->execute($channel, $payload);
        $replayed = app(IngestExternalOrder::class)->execute($channel, $payload);

        expect($rawConfiguration)->not->toContain('hidden')
            ->and($channel->refresh()->configuration)->toBe(['secret' => 'hidden'])
            ->and($first->getKey())->toBe($replayed->getKey())
            ->and(fn () => app(IngestExternalOrder::class)->execute($channel, ['external_order_id' => 'partner-100', 'payload' => ['total' => 300]]))->toThrow(HttpException::class);

        DB::table('qr_ordering_order_intents')->insert([
            ['status' => 'accepted', 'total' => 230, 'submitted_at' => now()],
            ['status' => 'pending_edge', 'total' => 90, 'submitted_at' => now()],
        ]);
        $report = app(RestaurantOperationalReport::class);
        $overview = $report->overview(now()->subDay(), now()->addDay());
        $daily = $report->daily(now()->subDay(), now()->addDay());

        expect($overview['qr_orders']['count'])->toBe(2)
            ->and($overview['qr_orders']['by_status']['accepted'])->toBe(1)
            ->and($overview['kds_tickets']['available'])->toBeFalse()
            ->and($daily['rows'][0]['accepted_total'])->toBe(230.0);
    });
});
