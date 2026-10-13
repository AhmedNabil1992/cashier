<?php

namespace Webkul\RestaurantReports;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class RestaurantReportsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'restaurant-reports';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }
    }

    public function boot(Panel $panel): void {}
}
