<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Webkul\RestaurantTables\Http\Controllers\FloorController;
use Webkul\RestaurantTables\Http\Controllers\ReservationController;
use Webkul\RestaurantTables\Http\Controllers\RestaurantTableController;
use Webkul\RestaurantTables\Http\Controllers\TableSessionProjectionController;

Route::middleware([InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class, 'tenant.plugin:restaurant-tables', 'auth:sanctum'])->group(function (): void {
    Route::apiResource('admin/api/v1/restaurant-tables/floors', FloorController::class);
    Route::apiResource('admin/api/v1/restaurant-tables/floors.tables', RestaurantTableController::class);
    Route::apiResource('admin/api/v1/restaurant-tables/reservations', ReservationController::class);
    Route::get('admin/api/v1/restaurant-tables/session-projections', [TableSessionProjectionController::class, 'index']);
    Route::post('api/v1/edge/restaurant-tables/session-projections', [TableSessionProjectionController::class, 'store'])
        ->middleware('edge.device:restaurant-tables:sync');
});
