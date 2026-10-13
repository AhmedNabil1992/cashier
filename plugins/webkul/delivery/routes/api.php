<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Webkul\Delivery\Http\Controllers\ChannelController;
use Webkul\Delivery\Http\Controllers\DriverController;
use Webkul\Delivery\Http\Controllers\ExternalOrderController;
use Webkul\Delivery\Http\Controllers\TaskProjectionController;
use Webkul\Delivery\Http\Controllers\ZoneController;

Route::middleware([InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class, 'tenant.plugin:delivery', 'auth:sanctum'])->group(function (): void {
    Route::apiResource('admin/api/v1/delivery/zones', ZoneController::class);
    Route::apiResource('admin/api/v1/delivery/drivers', DriverController::class);
    Route::apiResource('admin/api/v1/delivery/channels', ChannelController::class);
    Route::post('api/v1/integrations/delivery/channels/{channel}/orders', [ExternalOrderController::class, 'ingest'])
        ->middleware('edge.device:delivery:ingest');
    Route::get('api/v1/edge/delivery/external-orders', [ExternalOrderController::class, 'pending'])
        ->middleware('edge.device:delivery:sync');
    Route::patch('api/v1/edge/delivery/external-orders/{externalOrder}', [ExternalOrderController::class, 'acknowledge'])
        ->middleware('edge.device:delivery:sync');
    Route::get('admin/api/v1/delivery/task-projections', [TaskProjectionController::class, 'index']);
    Route::post('api/v1/edge/delivery/task-projections', [TaskProjectionController::class, 'store'])
        ->middleware('edge.device:delivery:sync');
});
