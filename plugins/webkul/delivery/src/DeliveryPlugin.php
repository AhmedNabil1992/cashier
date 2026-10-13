<?php

namespace Webkul\Delivery;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class DeliveryPlugin implements Plugin
{
    public function getId(): string
    {
        return 'delivery';
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
