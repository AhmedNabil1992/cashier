<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Webkul\RestaurantKds\Http\Controllers\StationController;
use Webkul\RestaurantKds\Http\Controllers\TicketProjectionController;

Route::middleware([InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class, 'tenant.plugin:restaurant-kds', 'auth:sanctum'])->group(function (): void {
    Route::apiResource('admin/api/v1/restaurant-kds/stations', StationController::class);
    Route::get('admin/api/v1/restaurant-kds/ticket-projections', [TicketProjectionController::class, 'index']);
    Route::post('api/v1/edge/restaurant-kds/ticket-projections', [TicketProjectionController::class, 'store'])
        ->middleware('edge.device:restaurant-kds:sync');
});
