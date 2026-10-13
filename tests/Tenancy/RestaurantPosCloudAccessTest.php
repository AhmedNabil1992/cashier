<?php

declare(strict_types=1);

use App\Jobs\ProvisionTenantDatabase;
use App\Models\Tenant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TenancyTestCase;
use Webkul\PluginManager\Models\Plugin;
use Webkul\RestaurantPos\Actions\PublishCatalog;
use Webkul\RestaurantPos\Actions\SaveMenu;
use Webkul\RestaurantPos\Actions\SaveModifierGroup;
use Webkul\RestaurantPos\Models\Profile;

uses(TenancyTestCase::class);

it('hides restaurant pos cloud APIs until the tenant plugin is installed and active', function () {
    $tenant = Tenant::query()->create(['id' => 'restaurant']);
    ProvisionTenantDatabase::dispatchSync($tenant->getTenantKey());
    $tenant->domains()->create(['domain' => 'restaurant.example.test']);
    $url = 'http://restaurant.example.test/admin/api/v1/restaurant-pos/profiles';

    $this->getJson($url)->assertNotFound();

    $tenant->run(function (): void {
        Plugin::query()->create([
            'name'         => 'restaurant-pos',
            'is_installed' => true,
            'is_active'    => false,
        ]);
    });

    $this->getJson($url)->assertNotFound();

    $tenant->run(function (): void {
        Plugin::query()->where('name', 'restaurant-pos')->update(['is_active' => true]);
    });

    $this->getJson($url)->assertUnauthorized();
});

it('publishes immutable versioned catalog snapshots for a restaurant pos profile', function () {
    $tenant = Tenant::query()->create(['id' => 'restaurant']);
    ProvisionTenantDatabase::dispatchSync($tenant->getTenantKey());

    $tenant->run(function (): void {
        Schema::create('products_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('products_products', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('reference')->nullable();
            $table->string('barcode')->nullable();
            $table->decimal('price', 15, 4)->nullable();
            $table->boolean('enable_sales')->default(true);
            $table->boolean('is_configurable')->default(false);
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->foreignId('company_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        (require base_path('plugins/webkul/restaurant-pos/database/migrations/tenant/2026_09_23_100000_create_restaurant_pos_profiles_table.php'))->up();
        (require base_path('plugins/webkul/restaurant-pos/database/migrations/tenant/2026_09_23_100001_create_restaurant_pos_catalog_publications_table.php'))->up();
        (require base_path('plugins/webkul/restaurant-pos/database/migrations/tenant/2026_09_23_100002_create_restaurant_pos_menus_table.php'))->up();
        (require base_path('plugins/webkul/restaurant-pos/database/migrations/tenant/2026_09_23_100003_create_restaurant_pos_modifier_groups_table.php'))->up();
        (require base_path('plugins/webkul/restaurant-pos/database/migrations/tenant/2026_09_23_100004_create_restaurant_pos_menu_pricing_and_combos_tables.php'))->up();

        $partnerId = DB::table('partners_partners')->insertGetId([
            'name'       => 'Cairo Cafe',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $otherPartnerId = DB::table('partners_partners')->insertGetId([
            'name'       => 'Other Branch',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $companyId = DB::table('companies')->insertGetId([
            'name'       => 'Cairo Cafe',
            'company_id' => 'CAIRO',
            'partner_id' => $partnerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $otherCompanyId = DB::table('companies')->insertGetId([
            'name'       => 'Other Branch',
            'company_id' => 'OTHER',
            'partner_id' => $otherPartnerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $categoryId = DB::table('products_categories')->insertGetId([
            'name'       => 'Drinks',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ([
            ['name' => 'Shared Water', 'company_id' => null, 'enable_sales' => true],
            ['name' => 'Cairo Coffee', 'company_id' => $companyId, 'enable_sales' => true],
            ['name' => 'Hidden Product', 'company_id' => $companyId, 'enable_sales' => false],
            ['name' => 'Other Coffee', 'company_id' => $otherCompanyId, 'enable_sales' => true],
        ] as $product) {
            DB::table('products_products')->insert($product + [
                'category_id' => $categoryId,
                'price'       => 10,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $profile = Profile::query()->create([
            'company_id'          => $companyId,
            'code'                => 'cairo-main',
            'name'                => 'Cairo Main POS',
            'default_order_type'  => 'takeaway',
            'enabled_order_types' => ['takeaway', 'dine_in'],
        ]);
        $cairoCoffeeId = DB::table('products_products')->where('name', 'Cairo Coffee')->value('id');
        $sharedWaterId = DB::table('products_products')->where('name', 'Shared Water')->value('id');
        DB::table('products_products')->where('id', $cairoCoffeeId)->update(['is_configurable'=>true]);
        DB::table('products_products')->insert([
            'name'        => 'Cairo Coffee Large', 'parent_id'=>$cairoCoffeeId, 'company_id'=>$companyId,
            'enable_sales'=> true, 'category_id'=>$categoryId, 'price'=>14, 'created_at'=>now(), 'updated_at'=>now(),
        ]);
        $milkGroup = app(SaveModifierGroup::class)->execute($profile, [
            'code'               => 'milk-choice',
            'name'               => 'Milk Choice',
            'minimum_selections' => 1,
            'maximum_selections' => 1,
            'free_allowance'     => 1,
            'is_required'        => true,
            'options'            => [
                ['name'=>'Whole Milk', 'price_delta'=>0],
                ['name'=> 'Oat Milk', 'price_delta'=>8],
            ],
        ]);
        app(SaveMenu::class)->execute($profile, [
            'code'         => 'all-day',
            'name'         => 'All Day Menu',
            'channels'     => ['pos', 'qr'],
            'availability' => ['days'=>['sun', 'mon'], 'from'=>'08:00', 'to'=>'23:00'],
            'categories'   => [[
                'name'  => 'Hot Drinks',
                'sort'  => 10,
                'items' => [[
                    'product_id'     => $cairoCoffeeId,
                    'display_name'   => 'Cairo Signature Coffee',
                    'price_override' => 15,
                    'sort'           => 10,
                    'modifier_groups'=> [['id'=>$milkGroup->getKey(), 'sort'=>10]],
                    'prices'         => [[
                        'channel'  => 'delivery', 'order_type'=>'delivery', 'amount'=>20,
                        'starts_at'=> '2026-09-23T00:00:00Z', 'ends_at'=>'2026-12-31T23:59:59Z', 'priority'=>10,
                    ]],
                    'combo_groups'   => [[
                        'code'   => 'drink-side', 'name'=>'Choose a Side', 'minimum_selections'=>1, 'maximum_selections'=>1,
                        'choices'=> [['product_id'=>$sharedWaterId, 'display_name'=>'Water', 'price_delta'=>2, 'is_default'=>true]],
                    ]],
                ]],
            ]],
        ]);
        $publisher = app(PublishCatalog::class);
        $first = $publisher->execute($profile);
        $second = $publisher->execute($profile);

        expect($first->version)->toBe(1)
            ->and($second->version)->toBe(2)
            ->and($first->checksum)->toHaveLength(64)
            ->and(collect($first->payload['products'])->pluck('name')->all())
            ->toBe(['Shared Water', 'Cairo Coffee', 'Cairo Coffee Large'])
            ->and($first->payload['products'][2]['parent_id'])->toBe($cairoCoffeeId)
            ->and($first->payload['menus'][0]['code'])->toBe('all-day')
            ->and($first->payload['menus'][0]['categories'][0]['items'][0]['name'])->toBe('Cairo Signature Coffee')
            ->and((float) $first->payload['menus'][0]['categories'][0]['items'][0]['price'])->toBe(15.0)
            ->and($first->payload['menus'][0]['categories'][0]['items'][0]['modifier_group_ids'])->toBe([$milkGroup->getKey()])
            ->and($first->payload['modifier_groups'][0]['options'][1]['name'])->toBe('Oat Milk')
            ->and((float) $first->payload['menus'][0]['categories'][0]['items'][0]['prices'][0]['amount'])->toBe(20.0)
            ->and($first->payload['menus'][0]['categories'][0]['items'][0]['combo_groups'][0]['choices'][0]['name'])->toBe('Water')
            ->and($profile->refresh()->published_at)->not->toBeNull();

        $foamGroup = app(SaveModifierGroup::class)->execute($profile, [
            'code'               => 'foam-choice',
            'name'               => 'Foam Choice',
            'minimum_selections' => 0,
            'maximum_selections' => 1,
            'options'            => [['name'=>'Add Foam', 'nested_modifier_group_id'=>$milkGroup->getKey()]],
        ]);

        expect(fn () => app(SaveModifierGroup::class)->execute($profile, [
            'code'               => 'milk-choice',
            'name'               => 'Milk Choice',
            'minimum_selections' => 1,
            'maximum_selections' => 1,
            'is_required'        => true,
            'options'            => [['name'=>'Oat Milk', 'nested_modifier_group_id'=>$foamGroup->getKey()]],
        ], $milkGroup))->toThrow(ValidationException::class, 'circular reference');

        expect(fn () => app(SaveMenu::class)->execute($profile, [
            'code'       => 'invalid-overlap',
            'name'       => 'Invalid Overlap',
            'channels'   => ['delivery'],
            'categories' => [[
                'name'  => 'Delivery',
                'items' => [[
                    'product_id' => $cairoCoffeeId,
                    'prices'     => [
                        ['channel'=>'delivery', 'amount'=>20, 'starts_at'=>'2026-10-01T00:00:00Z', 'ends_at'=>'2026-11-01T00:00:00Z', 'priority'=>10],
                        ['channel'=> 'delivery', 'amount'=>25, 'starts_at'=>'2026-10-15T00:00:00Z', 'ends_at'=>'2026-12-01T00:00:00Z', 'priority'=>10],
                    ],
                ]],
            ]],
        ]))->toThrow(ValidationException::class, 'cannot overlap');
    });
});
