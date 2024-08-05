<?php

use App\Http\Controllers\Analytics\AnalyticController;
use App\Http\Controllers\Analytics\AnalyticsFiltersController;
use App\Http\Controllers\Analytics\AnalyticsViewController;

Route::get('analytics', AnalyticsViewController::class)
    ->name('analytics.index');

Route::get('analytics/{group}/{subGroup?}', AnalyticsViewController::class)
    ->where(
        'group',
        'taxa|demographics|origin|location|circumstances-of-admission|clinical-classifications|disposition|hotline'
    );

Route::put('analytics/filters', [AnalyticsFiltersController::class, 'update']);

Route::get('analytics/{type}/{name}', [AnalyticController::class, 'show']);
