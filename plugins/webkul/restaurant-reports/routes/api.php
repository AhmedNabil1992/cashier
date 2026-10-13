<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Webkul\RestaurantReports\Http\Controllers\RestaurantReportController;

Route::prefix('admin/api/v1/restaurant-reports')->middleware([InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class, 'tenant.plugin:restaurant-reports', 'auth:sanctum'])->group(function (): void {
    Route::get('overview', [RestaurantReportController::class, 'overview']);
    Route::get('daily', [RestaurantReportController::class, 'daily']);
});
