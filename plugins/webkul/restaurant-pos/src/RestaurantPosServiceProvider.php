<?php

namespace Webkul\RestaurantPos;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class RestaurantPosServiceProvider extends PackageServiceProvider
{
    public static string $name = 'restaurant-pos';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasMigrations([
                '2026_09_23_100000_create_restaurant_pos_profiles_table',
                '2026_09_23_100001_create_restaurant_pos_catalog_publications_table',
                '2026_09_23_100002_create_restaurant_pos_menus_table',
                '2026_09_23_100003_create_restaurant_pos_modifier_groups_table',
                '2026_09_23_100004_create_restaurant_pos_menu_pricing_and_combos_tables',
            ])
            ->runsMigrations()
            ->hasDependencies(['products', 'sales', 'payments'])
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->installDependencies()->runsMigrations();
            })
            ->icon('restaurant-pos');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(RestaurantPosPlugin::make());
        });
    }

    public function packageBooted(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
