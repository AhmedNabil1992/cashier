<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Webkul\RestaurantPos\Http\Controllers\LatestCatalogController;
use Webkul\RestaurantPos\Http\Controllers\MenuController;
use Webkul\RestaurantPos\Http\Controllers\ModifierGroupController;
use Webkul\RestaurantPos\Http\Controllers\ProfileController;
use Webkul\RestaurantPos\Http\Controllers\PublishCatalogController;

Route::prefix('admin/api/v1/restaurant-pos')
    ->name('admin.api.v1.restaurant-pos.')
    ->middleware([
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class,
        'tenant.plugin:restaurant-pos',
        'auth:sanctum',
    ])->group(function (): void {
        Route::apiResource('profiles', ProfileController::class)->only(['index', 'store', 'show', 'update']);
        Route::apiResource('profiles.menus', MenuController::class);
        Route::apiResource('profiles.modifier-groups', ModifierGroupController::class);
        Route::post('profiles/{profile}/catalog-publications', PublishCatalogController::class)->name('profiles.catalog.publish');
        Route::get('profiles/{profile}/catalog-publications/latest', LatestCatalogController::class)->name('profiles.catalog.latest');
    });
