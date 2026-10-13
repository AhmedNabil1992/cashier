<?php

namespace Webkul\RestaurantReports;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class RestaurantReportsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'restaurant-reports';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)->hasDependencies(['restaurant-pos'])->hasInstallCommand(fn (InstallCommand $command) => $command->installDependencies())->icon('restaurant-reports');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(RestaurantReportsPlugin::make()));
    }

    public function packageBooted(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
