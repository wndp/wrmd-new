<?php

use App\Http\Controllers\AdmissionYearController;
use App\Http\Controllers\Api\V2\CustomTerminologyController;
use App\Http\Controllers\Api\V2\FieldMetaController;
use App\Http\Controllers\Api\V2\MediaOrderController;
use App\Http\Controllers\Api\V2\NutritionCookbookSearchController;
use App\Http\Controllers\Api\V2\PatientClassificationController;
use App\Http\Controllers\Api\V2\PatientPredictionsController;
use App\Http\Controllers\Api\V2\PrescriptionFormularyController;
use App\Http\Controllers\Api\V2\SearchPeopleController;
use App\Http\Controllers\Api\V2\TrainingController;
use App\Http\Controllers\Api\V2\UnrecognizedAccountPatientController;
use App\Http\Controllers\Api\V2\UnrecognizedPatientController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Maintenance\NutritionCookbookController;
use App\Http\Controllers\RelationshipsController;
use App\Http\Controllers\Settings\UsersController;

Route::prefix('internal-api')->group(function () {
    Route::get('locale/{country}', LocaleController::class)->withoutMiddleware(['auth', 'verified', 'wrmd']);

    /** Unrecognized Patients */
    // Route::get('unrecognized-patients/account/{account?}', [UnrecognizedAccountPatientController::class, 'index']);
    Route::put('unrecognized-patients/account/{account?}', UnrecognizedAccountPatientController::class);
    Route::put('unrecognized-patients/patient/{patient}', UnrecognizedPatientController::class);
    // //Route::get('misidentified-patients/account/{account}', [MisidentifiedAccountPatientController::class, 'index']);
    // //Route::get('misidentified-patients', [MisidentifiedPatientController::class, 'index']);

    /** Classification Training and Predictions */
    Route::post('patients/{patient}/classification/{category}/training', [TrainingController::class, 'store']);
    Route::get('patients/{patient}/predictions/{category}', PatientPredictionsController::class);

    /** Custom Classification Terminology */
    Route::controller(CustomTerminologyController::class)->group(function () {
        Route::get('/custom-classification-terminology/{category}', 'index')
            ->name('custom-classification-terminology.index');

        Route::post('/custom-classification-terminology/{category}', 'store')
            ->name('custom-classification-terminology.store');

        Route::put('/custom-classification-terminology/{category}/{terminology}', 'update')
            ->name('custom-classification-terminology.update');

        Route::delete('/custom-classification-terminology/{category}/{terminology}', 'destroy')
            ->name('custom-classification-terminology.destroy');
    });

    Route::get('patients/{patient}/classifications/{category}', PatientClassificationController::class)->name('patients.classifications.index');

    // /** Misc */
    Route::get('admissions/year/{year}', AdmissionYearController::class)->name('admissions.year');

    Route::middleware('can:viewPeople')->group(function () {
        Route::get('people/search', SearchPeopleController::class)->name('people.search');
    });

    // Relationships
    Route::controller(RelationshipsController::class)->group(function () {
        Route::get('relationships/{model}', 'index')->name('internal-api.related.search');
        Route::get('relationships', 'show')->name('internal-api.related.show');
        Route::post('relationships', 'store')->name('internal-api.related.store');
    });

    /* Formulary */
    Route::get('prescriptions/formulary/{patient?}', PrescriptionFormularyController::class)->name('formulary.search');
    Route::get('nutrition/cookbook/{patient?}', NutritionCookbookSearchController::class)->name('cookbook.search');

    Route::get('users/search', [UsersController::class, 'search'])->name('users.search');

    Route::get('field-meta', FieldMetaController::class);

    Route::post('media/order', MediaOrderController::class)->can('manage-attachments')->name('media.order');
});
