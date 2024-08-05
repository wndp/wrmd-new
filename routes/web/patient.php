<?php

use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\BatchUpdateController;
use App\Http\Controllers\CageCardController;
use App\Http\Controllers\ContinuedCareController;
use App\Http\Controllers\DetachRescuerController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\DuplicationController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\Expenses\ExpensesController;
use App\Http\Controllers\InitialCareController;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\IntakeExamController;
use App\Http\Controllers\Labs\LabsController;
use App\Http\Controllers\Necropsy\NecropsyCarcassController;
use App\Http\Controllers\Necropsy\NecropsyController;
use App\Http\Controllers\Necropsy\NecropsyMorphometricsController;
use App\Http\Controllers\Necropsy\NecropsySummaryController;
use App\Http\Controllers\Necropsy\NecropsySystemsController;
use App\Http\Controllers\NutritionPlanController;
use App\Http\Controllers\OutcomeController;
use App\Http\Controllers\PatientAnalyticsController;
use App\Http\Controllers\PatientLocationsController;
use App\Http\Controllers\PatientMetaController;
use App\Http\Controllers\PatientNotificationsController;
use App\Http\Controllers\PatientRevisionsController;
use App\Http\Controllers\PatientUnVoidController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\PatientsQuickAdmitController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\RecheckController;
use App\Http\Controllers\RescuerController;
use App\Http\Controllers\Research\AuxiliaryMarkerController;
use App\Http\Controllers\Research\BandingController;
use App\Http\Controllers\Research\MorphometricsController;
use App\Http\Controllers\Research\RecaptureController;
use App\Http\Controllers\Research\ResearchController;
use App\Http\Controllers\TreatmentLogController;
use App\Http\Controllers\VitalsController;

Route::prefix('patients')->name('patients.')->group(function () {
    Route::put('{voidedPatient}/unvoid', PatientUnVoidController::class)->can('un-void')->name('unvoid.update');
    Route::get('{patient}/notifications', PatientNotificationsController::class)->name('notifications');

    Route::controller(PatientsController::class)->group(function () {
        Route::get('create', 'create')->can('create-patients')->name('create');
        Route::post('', 'store')->can('create-patients')->name('store');
        Route::get('', 'index')->name('index');
    });

    Route::controller(PatientsQuickAdmitController::class)->middleware('can:create-patients')->group(function () {
        Route::get('quick-admit', 'create')->name('quick_admit.create');
        Route::post('quick-admit', 'store')->name('quick_admit.store');
    });

    Route::get('rescuer', RescuerController::class)->middleware('can:displayRescuer')->name('rescuer.edit');
    Route::get('initial', InitialCareController::class)->name('initial.edit');
    Route::get('continued', ContinuedCareController::class)->name('continued.edit');
    Route::get('analytics', PatientAnalyticsController::class)->name('analytics');

    Route::controller(DuplicationController::class)->middleware('can:create-patients')->group(function () {
        Route::get('duplicate', 'create')->name('duplicate.create');
        Route::post('duplicate/{patient}', 'store')->name('duplicate.store');
    });

    Route::delete('{patient}/detach-rescuer', DetachRescuerController::class)->name('detach_rescuer.destroy');

    Route::put('{patient}/intake-exam', IntakeExamController::class)->name('intake_exam.update');

    Route::controller(ExamController::class)->middleware('can:display-exams')->group(function () {
        Route::get('exams', 'index')->name('exam.index');
        Route::get('exams/{exam}/edit', 'edit')->name('exam.edit');
        Route::middleware('can:manage-exams')->group(function () {
            Route::get('exams/create', 'create')->name('exam.create');
            Route::post('{patient}/exam', 'store')->name('exam.store');
            Route::put('{patient}/exam/{exam}', 'update')->name('exam.update');
            Route::delete('{patient}/exam/{exam}', 'destroy')->name('exam.destroy');
        });
    });

    Route::controller(TreatmentLogController::class)->middleware('can:manage-treatment-logs')->group(function () {
        Route::post('{patient}/treatment-log', 'store')->name('treatment_log.store');
        Route::put('{patient}/treatment-log/{treatment_log}', 'update')->name('treatment_log.update');
        Route::delete('{patient}/treatment-log/{treatment_log}', 'destroy')->name('treatment_log.destroy');
    });

    Route::controller(VitalsController::class)->middleware('can:manage-exams')->group(function () {
        Route::post('{patient}/vitals', 'store')->name('vital.store');
        Route::put('{patient}/vitals/{vital}', 'update')->name('vital.update');
        Route::delete('{patient}/vitals/{vital}', 'destroy')->name('vital.destroy');
    });

    Route::put('{patient}/meta', PatientMetaController::class)->can('update-meta')->name('meta.update');
    Route::put('{patient}/cage-card', CageCardController::class)->can('update-cage-card')->name('cage_card.update');

    Route::middleware('can:update-patient-care')->group(function () {
        Route::put('{patient}/intake', IntakeController::class)->name('intake.update');
        Route::put('{patient}/diagnosis', DiagnosisController::class)->name('diagnosis.update');
        Route::put('{patient}/outcome', OutcomeController::class)->name('outcome.update');
    });

    Route::controller(RecheckController::class)->middleware('can:manage-daily-tasks')->group(function () {
        Route::post('{patient}/recheck', 'store')->name('recheck.store');
        Route::put('{patient}/recheck/{recheck}', 'update')->name('recheck.update');
    });

    Route::controller(PrescriptionController::class)->middleware('can:manage-daily-tasks')->group(function () {
        Route::post('{patient}/prescription', 'store')->name('prescription.store');
        Route::put('{patient}/prescription/{prescription}', 'update')->name('prescription.update');
    });

    Route::controller(NutritionPlanController::class)->middleware('can:manage-daily-tasks')->group(function () {
        Route::post('{patient}/nutrition', 'store')->name('nutrition.store');
        Route::put('{patient}/nutrition/{nutrition}', 'update')->name('nutrition.update');
    });

    Route::controller(PatientLocationsController::class)->middleware('can:manage-locations')->group(function () {
        Route::post('{patient}/location', 'store')->name('location.store');
        Route::put('{patient}/location/{location}', 'update')->name('location.update');
        Route::delete('{patient}/location/{location}', 'destroy')->name('location.destroy');
    });

    Route::get('/necropsy', [NecropsyController::class, 'edit'])->name('necropsy.edit');
    Route::name('necropsy.')->middleware('can:update-necropsy')->group(function () {
        Route::put('/{patient}/necropsy', [NecropsyController::class, 'update'])->name('update');
        Route::put('/{patient}/necropsy/carcass', NecropsyCarcassController::class)->name('carcass.update');
        Route::put('/{patient}/necropsy/morphometrics', NecropsyMorphometricsController::class)->name('morphometrics.update');
        Route::put('/{patient}/necropsy/systems', NecropsySystemsController::class)->name('systems.update');
        Route::put('/{patient}/necropsy/summary', NecropsySummaryController::class)->name('summary.update');
    });

    Route::get('/research', ResearchController::class)->name('research.edit');
    Route::name('research.')->middleware('can:update-research')->group(function () {
        Route::put('/{patient}/banding', BandingController::class)->name('banding.update');
        Route::put('/{patient}/auxiliary-marker', AuxiliaryMarkerController::class)->name('auxiliary_marker.update');
        Route::put('/{patient}/recapture', RecaptureController::class)->name('recapture.update');
        Route::put('/{patient}/morphometrics', MorphometricsController::class)->name('morphometrics.update');
    });

    Route::get('/attachments', AttachmentsController::class)->name('attachments.edit');

    Route::name('lab.')->middleware('can:manage-lab')->group(function () {
        Route::get('/lab', LabsController::class)->name('index');
    });

    Route::name('expenses.')->middleware('can:display-expenses')->group(function () {
        Route::get('/expenses', [ExpensesController::class, 'index'])->name('index');
        Route::middleware('can:manage-expenses')->group(function () {
            Route::post('/{patient}/expenses', [ExpensesController::class, 'store'])->name('store');
            Route::put('/{patient}/expenses/{transaction}', [ExpensesController::class, 'update'])->name('update');
            Route::delete('/{patient}/expenses/{transaction}', [ExpensesController::class, 'destroy'])->name('destroy');
        });
    });

    Route::controller(BatchUpdateController::class)->middleware('can:batch-update')->group(function () {
        Route::get('batch', 'edit')->name('batch.edit');
        Route::put('batch', 'update')->name('batch.update');
        //Route::put('batch/api', [BatchUpdateApiController::class, 'update']);
    });

    Route::controller(PatientRevisionsController::class)->middleware('can:displayRevisions')->group(function () {
        Route::get('revisions', 'index')->name('revisions.index');
        Route::get('revisions/{revision}', 'show')->name('revisions.show');
    });
});
