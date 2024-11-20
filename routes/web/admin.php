<?php

use App\Http\Controllers\Admin\AccountSpoofController;
use App\Http\Controllers\Admin\AccountsActionsController;
use App\Http\Controllers\Admin\AccountsController;
use App\Http\Controllers\Admin\AccountsExtensionsController;
use App\Http\Controllers\Admin\AccountsMasterAccountController;
use App\Http\Controllers\Admin\AccountsMetaController;
use App\Http\Controllers\Admin\AccountsMisidentifiedPatientsController;
use App\Http\Controllers\Admin\AccountsReportsController;
use App\Http\Controllers\Admin\AccountsTestimonialsController;
use App\Http\Controllers\Admin\AccountsUnrecognizedPatientsController;
use App\Http\Controllers\Admin\AccountsUsersController;
use App\Http\Controllers\Admin\AdminAuthorizationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLocalizationController;
use App\Http\Controllers\Admin\AdminMaintenanceController;
use App\Http\Controllers\Admin\MisidentifiedController;
use App\Http\Controllers\Admin\UnrecognizedController;

Route::prefix('admin')->middleware('can:viewWrmdAdmin')->group(function () {
    Route::get('dashboard', AdminDashboardController::class)->name('admin.dashboard');

    Route::middleware('can:manageAccounts')->group(function () {
        Route::get('accounts/reports', [AccountsReportsController::class, 'index'])
            ->name('teams.reports');

        Route::resource('teams', AccountsController::class);

        Route::put('accounts/{account}/master-account', AccountsMasterAccountController::class)
            ->name('teams.update.master-account');

        Route::get('accounts/{account}/delete', [AccountsController::class, 'delete'])
            ->name('teams.delete');

        Route::get('accounts/{account}/users', AccountsUsersController::class)
            ->name('teams.show.users');

        Route::get('accounts/{account}/unrecognized', AccountsUnrecognizedPatientsController::class)
            ->name('teams.show.unrecognized');

        Route::get('accounts/{account}/misidentified', AccountsMisidentifiedPatientsController::class)
            ->name('teams.show.misidentified');

        Route::get('accounts/{account}/meta', AccountsMetaController::class)
            ->name('teams.show.meta');

        Route::get('accounts/{account}/extensions', AccountsExtensionsController::class)->name('teams.extensions.edit');

        Route::get('accounts/{account}/actions', AccountsActionsController::class)
            ->name('teams.show.actions');
    });

    Route::middleware('can:spoofAccounts')->group(function () {
        Route::post('accounts/spoof/{account}', AccountSpoofController::class)->name('accounts.spoof');
    });

    Route::middleware('can:manageTaxa')->group(function () {
        Route::get('taxa/unrecognized', UnrecognizedController::class)
            ->name('taxa.unrecognized.index');

        Route::get('taxa/misidentified', MisidentifiedController::class)
            ->name('taxa.misidentified.index');
    });

    Route::get('maintenance', [AdminMaintenanceController::class, 'index'])
        ->name('admin.maintenance');

    Route::get('localization', AdminLocalizationController::class)
        ->name('admin.localization');

    Route::post('maintenance/{script}', [AdminMaintenanceController::class, 'store'])
        ->name('admin.maintenance.dispatch');

    Route::get('authorization', [AdminAuthorizationController::class, 'edit'])
        ->name('admin.authorization');

    Route::put('authorization/{association}', [AdminAuthorizationController::class, 'update'])
        ->name('admin.authorization.update');

    Route::name('admin.')->group(function () {
        Route::resource('testimonials', AccountsTestimonialsController::class);
    });
});
