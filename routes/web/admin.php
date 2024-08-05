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
use App\Http\Controllers\Admin\ClassificationsMetaController;
use App\Http\Controllers\Admin\ClassificationsTrainingController;
use App\Http\Controllers\Admin\ClassificationsValidationController;
use App\Http\Controllers\Admin\MisidentifiedController;
use App\Http\Controllers\Admin\TaxonCommonNamesController;
use App\Http\Controllers\Admin\TaxonConservationStatusController;
use App\Http\Controllers\Admin\TaxonController;
use App\Http\Controllers\Admin\TaxonDistributionController;
use App\Http\Controllers\Admin\TaxonEcologyController;
use App\Http\Controllers\Admin\UnrecognizedController;

Route::prefix('admin')->middleware('can:viewAdmin')->group(function () {
    Route::get('dashboard', AdminDashboardController::class)->name('admin.dashboard');

    Route::middleware('can:manageAccounts')->group(function () {
        Route::get('accounts/reports', [AccountsReportsController::class, 'index'])
            ->name('accounts.reports');

        Route::resource('accounts', AccountsController::class);

        Route::put('accounts/{account}/master-account', AccountsMasterAccountController::class)
            ->name('accounts.update.master-account');

        Route::get('accounts/{account}/delete', [AccountsController::class, 'delete'])
            ->name('accounts.delete');

        Route::get('accounts/{account}/users', AccountsUsersController::class)
            ->name('accounts.show.users');

        Route::get('accounts/{account}/unrecognized', AccountsUnrecognizedPatientsController::class)
            ->name('accounts.show.unrecognized');

        Route::get('accounts/{account}/misidentified', AccountsMisidentifiedPatientsController::class)
            ->name('accounts.show.misidentified');

        Route::get('accounts/{account}/meta', AccountsMetaController::class)
            ->name('accounts.show.meta');

        Route::get('accounts/{account}/extensions', AccountsExtensionsController::class)->name('accounts.extensions.edit');

        Route::get('accounts/{account}/actions', AccountsActionsController::class)
            ->name('accounts.show.actions');
    });

    Route::middleware('can:spoofAccounts')->group(function () {
        Route::post('accounts/spoof/{account}', AccountSpoofController::class)->name('accounts.spoof');
    });

    Route::middleware('can:manageTaxa')->group(function () {
        Route::get('taxa/unrecognized', UnrecognizedController::class)
            ->name('taxa.unrecognized.index');

        Route::get('taxa/misidentified', MisidentifiedController::class)
            ->name('taxa.misidentified.index');

        Route::resource('taxa', TaxonController::class);

        Route::resource('taxa.common-names', TaxonCommonNamesController::class);

        Route::get('taxa/{taxon}/ecology', [TaxonEcologyController::class, 'index'])
            ->name('taxa.ecology.index');

        Route::put('taxa/{taxon}/ecology', [TaxonEcologyController::class, 'update'])
            ->name('taxa.ecology.update');

        Route::get('taxa/{taxon}/distribution', [TaxonDistributionController::class, 'index'])
            ->name('taxa.distribution.index');

        Route::put('taxa/{taxon}/distribution', [TaxonDistributionController::class, 'update'])
            ->name('taxa.distribution.update');

        Route::get('taxa/{taxon}/conservation-status', [TaxonConservationStatusController::class, 'index'])
            ->name('taxa.conservation-status.index');

        Route::post('taxa/{taxon}/conservation-status', [TaxonConservationStatusController::class, 'store'])
            ->name('taxa.conservation-status.store');

        Route::put('taxa/{taxon}/conservation-status/{authority}/{territory}', [TaxonConservationStatusController::class, 'update'])
            ->name('taxa.conservation-status.update');

        Route::delete('taxa/{taxon}/conservation-status/{authority}/{territory}', [TaxonConservationStatusController::class, 'destroy'])
            ->name('taxa.conservation-status.destroy');
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

    Route::get('classification-training', [ClassificationsTrainingController::class, 'index'])
        ->name('classification.training.show');

    Route::get('classification-validation', [ClassificationsValidationController::class, 'index'])
        ->name('classification.validation.show');

    Route::put('classification-validation/{category}/{patient}', [ClassificationsValidationController::class, 'update'])
        ->name('classification.validation.update');

    Route::get('classification-counts', [ClassificationsMetaController::class, 'counts'])
        ->name('classification.counts.show');

    Route::get('classification-hierarchy', [ClassificationsMetaController::class, 'hierarchy'])
        ->name('classification.hierarchy.show');

    Route::name('admin.')->group(function () {
        Route::resource('testimonials', AccountsTestimonialsController::class);
    });
});
