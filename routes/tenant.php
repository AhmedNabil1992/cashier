<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Edge\EdgeDeviceController;
use App\Http\Controllers\Api\Edge\SyncEventController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/tenant/health', fn () => response()->json([
        'status'    => 'ok',
        'tenant_id' => tenant('id'),
    ]))->name('tenant.health');
});

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function (): void {
    Route::prefix('admin/api/v1/edge-devices')
        ->middleware(['auth:sanctum', 'edge.admin'])
        ->group(function (): void {
            Route::get('/', [EdgeDeviceController::class, 'index'])->name('tenant.edge-devices.index');
            Route::post('/', [EdgeDeviceController::class, 'store'])->name('tenant.edge-devices.store');
            Route::post('{edgeDevice}/rotate-token', [EdgeDeviceController::class, 'rotate'])->name('tenant.edge-devices.rotate');
            Route::post('{edgeDevice}/revoke', [EdgeDeviceController::class, 'revoke'])->name('tenant.edge-devices.revoke');
        });

    Route::post('api/v1/edge/sync/events', SyncEventController::class)
        ->middleware(['auth:sanctum', 'edge.device:edge:sync', 'throttle:120,1'])
        ->name('tenant.edge.sync.events');
});
