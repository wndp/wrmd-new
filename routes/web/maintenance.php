<?php

use App\Enums\Ability;
use App\Http\Controllers\Maintenance\AutocompleteController;
use App\Http\Controllers\Maintenance\CustomFieldsController;
use App\Http\Controllers\Maintenance\DeletePatientController;
use App\Http\Controllers\Maintenance\DeleteYearController;
use App\Http\Controllers\Maintenance\ExpenseCategoriesController;
use App\Http\Controllers\Maintenance\FormularyController;
use App\Http\Controllers\Maintenance\NutritionCookbookController;
use App\Http\Controllers\Maintenance\OwcnIoaMaintenanceController;
use App\Http\Controllers\Maintenance\PaperformsTemplatesController;
use App\Http\Controllers\Maintenance\TransfersController;
use App\Http\Controllers\Maintenance\UncollaborateTransferController;
use App\Http\Controllers\Maintenance\UnrecognizedPatientsController;
use Illuminate\Auth\Middleware\Authorize;

Route::prefix('maintenance')->name('maintenance.')->group(function () {
    Route::get('/unrecognized-patients', UnrecognizedPatientsController::class)
        ->middleware(Authorize::using(Ability::VIEW_ACCOUNT_MAINTENANCE))
        ->name('unrecognized-patients');

    Route::get('/transfers', TransfersController::class)->name('transfers');
    Route::post('/transfers/{transfer}/uncollaborate', UncollaborateTransferController::class)->name('transfers.uncollaborate');

    Route::controller(FormularyController::class)->middleware(Authorize::using(Ability::VIEW_ACCOUNT_MAINTENANCE))->group(function () {
        Route::get('/prescription-formulary', 'index')->name('formulas.index');
        Route::get('/prescription-formulary/create', 'create')->name('formulas.create');
        Route::post('/prescription-formulary', 'store')->name('formulas.store');
        Route::get('/prescription-formulary/{formula}/edit', 'edit')->name('formulas.edit');
        Route::put('/prescription-formulary/{formula}', 'update')->name('formulas.update');
        Route::delete('/prescription-formulary/{formula}', 'destroy')->name('formulas.destroy');
    });

    Route::controller(NutritionCookbookController::class)->middleware(Authorize::using(Ability::VIEW_ACCOUNT_MAINTENANCE))->group(function () {
        Route::get('/nutrition-cookbook', 'index')->name('cookbook.index');
        Route::get('/nutrition-cookbook/create', 'create')->name('cookbook.create');
        Route::post('/nutrition-cookbook', 'store')->name('cookbook.store');
        Route::get('/nutrition-cookbook/{formula}/edit', 'edit')->name('cookbook.edit');
        Route::put('/nutrition-cookbook/{formula}', 'update')->name('cookbook.update');
        Route::delete('/nutrition-cookbook/{formula}', 'destroy')->name('cookbook.destroy');
    });

    Route::controller(AutocompleteController::class)->middleware(Authorize::using(Ability::VIEW_ACCOUNT_MAINTENANCE))->group(function () {
        Route::get('/autocomplete', 'index')->name('autocomplete.index');
        Route::post('/autocomplete', 'store')->name('autocomplete.store');
        Route::put('/autocomplete/{field}', 'update')->name('autocomplete.update');
        Route::delete('/autocomplete/{field}', 'destroy')->name('autocomplete.destroy');
    });

    Route::controller(ExpenseCategoriesController::class)->middleware(Authorize::using(Ability::MANAGE_EXPENSES))->group(function () {
        Route::get('/expense-categories', 'index')->name('expense_categories.index');
        Route::get('/expense-categories/create', 'create')->name('expense_categories.create');
        Route::post('/expense-categories', 'store')->name('expense_categories.store');
        Route::get('/expense-categories/{category}/edit', 'edit')->name('expense_categories.edit');
        Route::put('/expense-categories/{category}', 'update')->name('expense_categories.update');
        Route::delete('/expense-categories/{category}', 'destroy')->name('expense_categories.destroy');
    });

    Route::controller(PaperformsTemplatesController::class)->group(function () {
        Route::get('/paper-forms', 'index')->name('paper_forms.index');
        Route::post('/paper-forms', 'store')->name('paper_forms.store');
        Route::delete('/paper-forms/{slug}', 'destroy')->name('paper_forms.destroy');
    });

    Route::controller(CustomFieldsController::class)->middleware(Authorize::using(Ability::MANAGE_CUSTOM_FIELDS))->group(function () {
        Route::get('/custom-fields', 'index')->name('custom_fields.index');
        Route::get('/custom-fields/create', 'create')->name('custom_fields.create');
        Route::post('/custom-fields', 'store')->name('custom_fields.store');
        Route::get('/custom-fields/{customField}/edit', 'edit')->name('custom_fields.edit');
        Route::put('/custom-fields/{customField}', 'update')->name('custom_fields.update');
        Route::delete('/custom-fields/{customField}', 'destroy')->name('custom_fields.destroy');
    });

    Route::controller(OwcnIoaMaintenanceController::class)->group(function () {
        Route::get('/owcn-ioa', 'index')->name('owcn_ioa.index');
        Route::put('/owcn-ioa', 'update')->name('owcn_ioa.update');
    });
});

Route::prefix('danger')->middleware(Authorize::using(Ability::VIEW_DANGER_ZONE))->group(function () {
    Route::get('delete-patient', [DeletePatientController::class, 'index'])
        ->name('patient.delete.index');

    Route::delete('delete-patient', [DeletePatientController::class, 'destroy'])
        ->name('patient.delete.destroy');

    Route::get('delete-year', [DeleteYearController::class, 'index'])
        ->name('year.delete.index');

    Route::delete('delete-year', [DeleteYearController::class, 'destroy'])
        ->name('year.delete.destroy');
});
