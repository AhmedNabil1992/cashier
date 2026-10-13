<?php

namespace Webkul\QrOrdering;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class QrOrderingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'qr-ordering';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasMigrations([
                '2026_09_23_140000_create_qr_ordering_channels_table',
                '2026_09_23_140001_create_qr_ordering_guest_sessions_table',
                '2026_09_23_140002_create_qr_ordering_order_intents_table',
            ])->runsMigrations()->hasDependencies(['restaurant-pos'])
            ->hasInstallCommand(fn (InstallCommand $command) => $command->installDependencies()->runsMigrations())
            ->icon('qr-ordering');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(QrOrderingPlugin::make()));
    }

    public function packageBooted(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
