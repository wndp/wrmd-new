<?php

use App\Http\Controllers\Maintenance\AutocompleteController;
use App\Http\Controllers\Maintenance\CustomFieldsController;
use App\Http\Controllers\Maintenance\DeletePatientController;
use App\Http\Controllers\Maintenance\DeleteYearController;
use App\Http\Controllers\Maintenance\ExpenseCategoriesController;
use App\Http\Controllers\Maintenance\FormulaController;
use App\Http\Controllers\Maintenance\OwcnIoaMaintenanceController;
use App\Http\Controllers\Maintenance\PaperformsTemplatesController;
use App\Http\Controllers\Maintenance\TransfersController;
use App\Http\Controllers\Maintenance\UncollaborateTransferController;
use App\Http\Controllers\Maintenance\UnrecognizedPatientsController;

Route::prefix('maintenance')->name('maintenance.')->group(function () {
    Route::get('/unrecognized-patients', UnrecognizedPatientsController::class)
        ->can('viewMaintenance')
        ->name('unrecognized-patients');

    Route::get('/transfers', TransfersController::class)->name('transfers');
    Route::post('/transfers/{transfer:uuid}/uncollaborate', UncollaborateTransferController::class)->name('transfers.uncollaborate');

    Route::controller(FormulaController::class)->middleware('can:viewMaintenance')->group(function () {
        Route::get('/prescription-formulas', 'index')->name('formulas.index');
        Route::get('/prescription-formulas/create', 'create')->name('formulas.create');
        Route::post('/prescription-formulas', 'store')->name('formulas.store');
        Route::get('/prescription-formulas/{formula}/edit', 'edit')->name('formulas.edit');
        Route::put('/prescription-formulas/{formula}', 'update')->name('formulas.update');
        Route::delete('/prescription-formulas/{formula}', 'destroy')->name('formulas.destroy');
    });

    Route::controller(AutocompleteController::class)->middleware('can:viewMaintenance')->group(function () {
        Route::get('/autocomplete', 'index')->name('autocomplete.index');
        Route::post('/autocomplete', 'store')->name('autocomplete.store');
        Route::put('/autocomplete/{field}', 'update')->name('autocomplete.update');
        Route::delete('/autocomplete/{field}', 'destroy')->name('autocomplete.destroy');
    });

    Route::controller(ExpenseCategoriesController::class)->middleware('can:manage-expenses')->group(function () {
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

    Route::controller(CustomFieldsController::class)->middleware('can:manage-custom-fields')->group(function () {
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

Route::prefix('danger')->middleware('can:display-danger-zone')->group(function () {
    Route::get('delete-patient', [DeletePatientController::class, 'index'])
        ->name('patient.delete.index');

    Route::delete('delete-patient', [DeletePatientController::class, 'destroy'])
        ->name('patient.delete.destroy');

    Route::get('delete-year', [DeleteYearController::class, 'index'])
        ->name('year.delete.index');

    Route::delete('delete-year', [DeleteYearController::class, 'destroy'])
        ->name('year.delete.destroy');
});
