<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper;
use Webkul\Support\Database\Dialects\DatabaseDialect;
use Webkul\Support\Database\Dialects\MySqlDialect;

abstract class TenancyTestCase extends BaseTestCase
{
    protected bool $createdEnvironmentFile = false;

    protected string $centralDatabase;

    protected string $tenantDatabasePrefix;

    protected function setUp(): void
    {
        $environmentFile = dirname(__DIR__).'/.env';

        if (! file_exists($environmentFile)) {
            touch($environmentFile);
            $this->createdEnvironmentFile = true;
        }

        parent::setUp();

        $token = bin2hex(random_bytes(6));
        $this->centralDatabase = database_path("tenancy_test_central_{$token}.sqlite");
        $this->tenantDatabasePrefix = "tenancy_test_{$token}_";

        touch($this->centralDatabase);

        config([
            'app.key'                              => 'base64:'.base64_encode(random_bytes(32)),
            'database.default'                     => 'sqlite',
            'database.connections.sqlite.database' => $this->centralDatabase,
            'tenancy.database.central_connection'  => 'sqlite',
            'tenancy.database.prefix'              => $this->tenantDatabasePrefix,
            'tenancy.database.suffix'              => '.sqlite',
            'tenancy.bootstrappers'                => [DatabaseTenancyBootstrapper::class],
        ]);

        DB::purge('sqlite');

        $this->app->instance(DatabaseDialect::class, new MySqlDialect);

        Schema::create('tenants', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->timestamps();
            $table->json('data')->nullable();
        });

        Schema::create('domains', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('domain')->unique();
            $table->string('tenant_id');
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('platform_api_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('idempotency_key')->unique();
            $table->string('method', 10);
            $table->string('path');
            $table->char('request_hash', 64);
            $table->unsignedSmallInteger('response_status');
            $table->json('response_body');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        tenancy()->end();
        DB::purge('tenant');
        DB::purge('sqlite');

        foreach (glob(database_path($this->tenantDatabasePrefix.'*.sqlite')) ?: [] as $database) {
            @unlink($database);
        }

        if (isset($this->centralDatabase)) {
            @unlink($this->centralDatabase);
        }

        if ($this->createdEnvironmentFile) {
            @unlink(dirname(__DIR__).'/.env');
        }

        parent::tearDown();
    }
}
