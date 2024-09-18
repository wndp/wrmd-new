<?php

use App\Http\Controllers\Reports\FavoriteReportsController;
use App\Http\Controllers\Reports\ReportEmailController;
use App\Http\Controllers\Reports\ReportGeneratorController;
use App\Http\Controllers\Reports\ReportStreamController;
use App\Http\Controllers\Reports\ReportsController;
use Illuminate\Auth\Middleware\Authorize;

Route::middleware(Authorize::using('VIEW_REPORTS'))->group(function () {
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');

    Route::post('reports/favorite/{report}', [FavoriteReportsController::class, 'store']);

    Route::delete('reports/favorite/{report}', [FavoriteReportsController::class, 'destroy']);

    Route::get('reports/generated/{account?}', [ReportGeneratorController::class, 'index']);

    Route::get('reports/generate/{report}', [ReportGeneratorController::class, 'show'])->name('reports.generate');

    Route::post('reports/generate/{report}', [ReportGeneratorController::class, 'store']);

    Route::get('reports/stream', ReportStreamController::class);

    Route::post('reports/email/{report}', ReportEmailController::class);

    Route::get('reports/{report}', [ReportsController::class, 'show'])->name('reports.show');
});
