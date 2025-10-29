<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetricsController;

Route::prefix('metrics')->group(function () {
    Route::get('monthly-sales', [MetricsController::class, 'monthlySales']);
    Route::get('open-deals', [MetricsController::class, 'openDeals']);
    Route::get('active-accounts', [MetricsController::class, 'activeAccounts']);
    Route::get('daily-sales', [MetricsController::class, 'dailySales']);
});
