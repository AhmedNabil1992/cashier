<?php

namespace Webkul\RestaurantKds;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class RestaurantKdsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'restaurant-kds';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasMigrations([
                '2026_09_23_130000_create_restaurant_kds_stations_table',
                '2026_09_23_130001_create_restaurant_kds_routing_rules_table',
                '2026_09_23_130002_create_restaurant_kds_ticket_projections_table',
            ])->runsMigrations()->hasDependencies(['restaurant-pos'])
            ->hasInstallCommand(fn (InstallCommand $command) => $command->installDependencies()->runsMigrations())
            ->icon('restaurant-kds');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(RestaurantKdsPlugin::make()));
    }

    public function packageBooted(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
