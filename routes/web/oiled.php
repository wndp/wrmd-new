<?php

use App\Http\Controllers\Oiled\CaseSummaryController;
use App\Http\Controllers\Oiled\ConditioningController;
use App\Http\Controllers\Oiled\FieldStabilizationController;
use App\Http\Controllers\Oiled\IntakeExamController;
use App\Http\Controllers\Oiled\ProcessingCarcassConditionController;
use App\Http\Controllers\Oiled\ProcessingCollectionController;
use App\Http\Controllers\Oiled\ProcessingCommentsController;
use App\Http\Controllers\Oiled\ProcessingController;
use App\Http\Controllers\Oiled\ProcessingEvidenceController;
use App\Http\Controllers\Oiled\ProcessingOilingController;
use App\Http\Controllers\Oiled\WashController;

Route::prefix('patients/oiled')->name('oiled.')->group(function () {
    Route::get('case-summary', CaseSummaryController::class)->name('summary.edit');
    Route::get('field', [FieldStabilizationController::class, 'edit'])->name('field.edit');
    Route::put('{patient}/field-exam', [FieldStabilizationController::class, 'update'])->name('field.update');
    Route::get('intake', IntakeExamController::class)->name('intake.edit');

    Route::get('wash', [WashController::class, 'index'])->name('wash.index');
    Route::post('{patient}/wash', [WashController::class, 'store'])->name('wash.store');
    Route::put('{patient}/wash/{wash}', [WashController::class, 'update'])->name('wash.update');
    Route::delete('{patient}/wash/{wash}', [WashController::class, 'destroy'])->name('wash.destroy');

    Route::get('conditioning', [ConditioningController::class, 'index'])->name('conditioning.index');
    Route::post('{patient}/conditioning', [ConditioningController::class, 'store'])->name('conditioning.store');
    Route::put('{patient}/conditioning/{conditioning}', [ConditioningController::class, 'update'])->name('conditioning.update');
    Route::delete('{patient}/conditioning/{conditioning}', [ConditioningController::class, 'destroy'])->name('conditioning.destroy');

    Route::name('processing.')->group(function () {
        Route::get('processing', ProcessingController::class)->name('edit');
        Route::put('/{patient}/processing/collection', ProcessingCollectionController::class)->name('collection.update');
        Route::put('/{patient}/processing/evidence', ProcessingEvidenceController::class)->name('evidence.update');
        Route::put('/{patient}/processing/comments', ProcessingCommentsController::class)->name('comments.update');
        Route::put('/{patient}/processing/oiling', ProcessingOilingController::class)->name('oiling.update');
        Route::put('/{patient}/processing/carcass-condition', ProcessingCarcassConditionController::class)->name('carcassCondition.update');
    });
});
