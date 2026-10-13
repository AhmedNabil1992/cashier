<?php

namespace Webkul\RestaurantTables;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class RestaurantTablesServiceProvider extends PackageServiceProvider
{
    public static string $name = 'restaurant-tables';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)->hasMigrations([
            '2026_09_23_110000_create_restaurant_floors_table',
            '2026_09_23_110001_create_restaurant_tables_table',
            '2026_09_23_110002_create_restaurant_reservations_table',
            '2026_09_23_110003_create_restaurant_table_session_projections_table',
        ])->runsMigrations()->hasDependencies(['restaurant-pos'])
            ->hasInstallCommand(fn (InstallCommand $command) => $command->installDependencies()->runsMigrations())
            ->icon('restaurant-tables');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(RestaurantTablesPlugin::make()));
    }

    public function packageBooted(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
