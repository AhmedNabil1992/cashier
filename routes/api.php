<?php

use App\Http\Controllers\Api\Platform\CreateTenantController;
use App\Http\Controllers\Api\Platform\RecordPluginPurchaseController;
use App\Http\Controllers\Api\Platform\RenewTenantSubscriptionController;
use App\Http\Controllers\Api\Platform\ShowTenantProvisioningController;
use App\Http\Controllers\Api\Platform\SyncTenantPluginsController;
use Illuminate\Support\Facades\Route;

Route::prefix('platform/v1')->middleware('platform.signature')->group(function (): void {
    Route::post('tenants', CreateTenantController::class)
        ->middleware('platform.idempotency')
        ->name('platform.tenants.store');

    Route::get('tenants/{tenant}/provisioning', ShowTenantProvisioningController::class)
        ->name('platform.tenants.provisioning.show');

    Route::post('tenants/{tenant}/renewals', RenewTenantSubscriptionController::class)
        ->middleware('platform.idempotency')
        ->name('platform.tenants.renewals.store');

    Route::post('tenants/{tenant}/plugin-purchases', RecordPluginPurchaseController::class)
        ->middleware('platform.idempotency')
        ->name('platform.tenants.plugin-purchases.store');
});

Route::post('platform/v1/tenants/{tenant}/plugins', SyncTenantPluginsController::class)
    ->middleware('platform.signature')
    ->name('platform.tenants.plugins.sync');
