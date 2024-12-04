<?php

use App\Enums\Ability;
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
use App\Http\Controllers\Admin\AdminMaintenanceController;
use App\Http\Controllers\Admin\MisidentifiedController;
use App\Http\Controllers\Admin\TaxonController;
use App\Http\Controllers\Admin\UnrecognizedController;
use Illuminate\Auth\Middleware\Authorize;

Route::prefix('admin')->middleware(Authorize::using(Ability::VIEW_WRMD_ADMIN->value))->group(function () {
    Route::get('dashboard', AdminDashboardController::class)->name('admin.dashboard');

    Route::get('teams/reports', [AccountsReportsController::class, 'index'])
        ->name('teams.reports');

    Route::resource('teams', AccountsController::class);

    Route::put('teams/{team}/master-account', AccountsMasterAccountController::class)
        ->name('teams.update.master-account');

    Route::get('teams/{team}/delete', [AccountsController::class, 'delete'])
        ->name('teams.delete');

    Route::get('teams/{team}/users', AccountsUsersController::class)
        ->name('teams.show.users');

    Route::get('teams/{team}/unrecognized', AccountsUnrecognizedPatientsController::class)
        ->name('teams.show.unrecognized');

    Route::get('teams/{team}/misidentified', AccountsMisidentifiedPatientsController::class)
        ->name('teams.show.misidentified');

    Route::get('teams/{team}/meta', AccountsMetaController::class)
        ->name('teams.show.meta');

    Route::get('teams/{team}/extensions', AccountsExtensionsController::class)->name('teams.extensions.edit');

    Route::get('teams/{team}/actions', AccountsActionsController::class)
        ->name('teams.show.actions');

    Route::post('teams/spoof/{team}', AccountSpoofController::class)->name('teams.spoof');

    Route::get('taxa', TaxonController::class)
        ->name('taxa.index');

    Route::get('taxa/unrecognized', UnrecognizedController::class)
        ->name('taxa.unrecognized.index');

    Route::get('taxa/misidentified', MisidentifiedController::class)
        ->name('taxa.misidentified.index');

    Route::get('maintenance', [AdminMaintenanceController::class, 'index'])
        ->name('admin.maintenance');

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
