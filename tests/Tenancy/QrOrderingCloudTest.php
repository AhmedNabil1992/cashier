<?php

declare(strict_types=1);

use App\Models\Tenant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TenancyTestCase;
use Webkul\QrOrdering\Actions\AcknowledgeOrderIntent;
use Webkul\QrOrdering\Actions\SubmitOrderIntent;
use Webkul\QrOrdering\Models\Channel;
use Webkul\RestaurantPos\Models\CatalogPublication;

uses(TenancyTestCase::class);

it('quotes idempotently and waits for the newest edge acknowledgement', function () {
    $tenant = Tenant::query()->create(['id' => 'qr-cloud']);
    app()->call([new CreateDatabase($tenant), 'handle']);

    $tenant->run(function (): void {
        Schema::create('restaurant_pos_profiles', function (Blueprint $table): void {
            $table->id();
        });
        Schema::create('restaurant_pos_catalog_publications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('version');
            $table->char('checksum', 64);
            $table->json('payload');
            $table->timestamp('published_at');
            $table->timestamps();
        });
        Schema::create('qr_ordering_channels', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->string('slug');
            $table->string('name');
            $table->string('mode');
            $table->boolean('requires_table_token');
            $table->boolean('is_active');
            $table->json('settings')->nullable();
            $table->timestamps();
        });
        Schema::create('qr_ordering_guest_sessions', function (Blueprint $table): void {
            $table->id();
        });
        Schema::create('qr_ordering_order_intents', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->unsignedBigInteger('channel_id');
            $table->unsignedBigInteger('guest_session_id')->nullable();
            $table->string('idempotency_key');
            $table->char('request_hash', 64);
            $table->string('status');
            $table->unsignedBigInteger('catalog_version');
            $table->decimal('total', 15, 4);
            $table->json('payload');
            $table->uuid('edge_order_id')->nullable();
            $table->unsignedBigInteger('version');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
            $table->unique(['channel_id', 'idempotency_key']);
        });

        $profileId = Schema::getConnection()->table('restaurant_pos_profiles')->insertGetId([]);
        CatalogPublication::query()->create([
            'profile_id'   => $profileId,
            'version'      => 7,
            'checksum'     => str_repeat('a', 64),
            'published_at' => now(),
            'payload'      => [
                'profile' => ['enabled_order_types' => ['takeaway', 'dine_in']],
                'menus'   => [[
                    'channels'   => ['qr'],
                    'categories' => [[
                        'items' => [[
                            'id'                 => 11,
                            'product_id'         => 21,
                            'name'               => 'Burger',
                            'price'              => 100,
                            'prices'             => [],
                            'modifier_group_ids' => [31],
                        ]],
                    ]],
                ]],
                'modifier_groups' => [[
                    'id'                 => 31,
                    'name'               => 'Cheese',
                    'minimum_selections' => 0,
                    'maximum_selections' => 1,
                    'options'            => [[
                        'id'          => 41,
                        'name'        => 'Extra cheese',
                        'price_delta' => 15,
                    ]],
                ]],
            ],
        ]);
        $channel = Channel::query()->create([
            'profile_id'           => $profileId,
            'slug'                 => 'main-menu',
            'name'                 => 'Main Menu',
            'mode'                 => 'browse_and_order',
            'requires_table_token' => false,
            'is_active'            => true,
        ]);
        $attributes = [
            'idempotency_key' => 'guest-order-1',
            'catalog_version' => 7,
            'order_type'      => 'takeaway',
            'items'           => [[
                'menu_item_id'        => 11,
                'quantity'            => 2,
                'modifier_option_ids' => [41],
            ]],
        ];

        $first = app(SubmitOrderIntent::class)->execute($channel, null, $attributes);
        $replayed = app(SubmitOrderIntent::class)->execute($channel, null, $attributes);
        $conflictingReplay = $attributes;
        $conflictingReplay['items'][0]['quantity'] = 3;
        $acknowledged = app(AcknowledgeOrderIntent::class)->execute($first, [
            'status'        => 'accepted',
            'edge_order_id' => '018f47f0-92a2-7bc1-b37a-57dc00f72c20',
            'version'       => 2,
        ]);
        $stale = app(AcknowledgeOrderIntent::class)->execute($first, [
            'status'           => 'rejected',
            'version'          => 1,
            'rejection_reason' => 'stale response',
        ]);

        expect($first->getKey())->toBe($replayed->getKey())
            ->and((float) $first->total)->toBe(230.0)
            ->and($first->status)->toBe('pending_edge')
            ->and($acknowledged['applied'])->toBeTrue()
            ->and($acknowledged['order_intent']->status)->toBe('accepted')
            ->and($stale['applied'])->toBeFalse()
            ->and($stale['order_intent']->status)->toBe('accepted');

        expect(fn () => app(SubmitOrderIntent::class)->execute($channel, null, $conflictingReplay))
            ->toThrow(HttpException::class);
    });
});
