<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Webkul\QrOrdering\Http\Controllers\ChannelController;
use Webkul\QrOrdering\Http\Controllers\EdgeOrderIntentController;
use Webkul\QrOrdering\Http\Controllers\GuestSessionController;
use Webkul\QrOrdering\Http\Controllers\PublicMenuController;
use Webkul\QrOrdering\Http\Controllers\PublicOrderIntentController;

$tenantMiddleware = [
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'tenant.plugin:qr-ordering',
];

Route::prefix('admin/api/v1/qr-ordering')
    ->name('admin.api.v1.qr-ordering.')
    ->middleware([...$tenantMiddleware, 'auth:sanctum'])
    ->group(function (): void {
        Route::apiResource('channels', ChannelController::class);
        Route::post('channels/{channel}/guest-sessions', [GuestSessionController::class, 'store'])->name('channels.guest-sessions.store');
        Route::delete('channels/{channel}/guest-sessions/{guestSession}', [GuestSessionController::class, 'destroy'])->name('channels.guest-sessions.destroy');
    });

Route::prefix('api/v1/public/qr/{channel:slug}')
    ->name('api.v1.public.qr.')
    ->middleware([...$tenantMiddleware, 'throttle:60,1'])
    ->group(function (): void {
        Route::get('menu', PublicMenuController::class)->name('menu');
        Route::post('orders', [PublicOrderIntentController::class, 'store'])->name('orders.store');
        Route::get('orders/{orderIntent:public_id}', [PublicOrderIntentController::class, 'show'])->name('orders.show');
    });

Route::prefix('api/v1/edge/qr-ordering')
    ->name('api.v1.edge.qr-ordering.')
    ->middleware([...$tenantMiddleware, 'auth:sanctum', 'edge.device:qr-ordering:sync'])
    ->group(function (): void {
        Route::get('order-intents', [EdgeOrderIntentController::class, 'index'])->name('order-intents.index');
        Route::patch('order-intents/{orderIntent:public_id}', [EdgeOrderIntentController::class, 'update'])->name('order-intents.update');
    });
