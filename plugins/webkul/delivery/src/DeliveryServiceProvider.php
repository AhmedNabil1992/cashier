<?php

namespace Webkul\Delivery;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class DeliveryServiceProvider extends PackageServiceProvider
{
    public static string $name = 'delivery';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)->hasMigrations(['2026_09_23_120000_create_delivery_zones_table', '2026_09_23_120001_create_delivery_drivers_table', '2026_09_23_120002_create_delivery_channels_table', '2026_09_23_120003_create_delivery_external_orders_table', '2026_09_23_120004_create_delivery_task_projections_table', '2026_09_23_120005_encrypt_delivery_channel_configuration'])->runsMigrations()->hasDependencies(['restaurant-pos'])->hasInstallCommand(fn (InstallCommand $command) =>$command->installDependencies()->runsMigrations())->icon('delivery');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(fn (Panel $panel) =>$panel->plugin(DeliveryPlugin::make()));
    }

    public function packageBooted(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
