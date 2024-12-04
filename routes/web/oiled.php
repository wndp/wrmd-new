<?php

use App\Http\Controllers\Oiled\CaseSummaryController;
use App\Http\Controllers\Oiled\ProcessingCarcassConditionController;
use App\Http\Controllers\Oiled\ProcessingCollectionController;
use App\Http\Controllers\Oiled\ProcessingCommentsController;
use App\Http\Controllers\Oiled\ProcessingController;
use App\Http\Controllers\Oiled\ProcessingEvidenceController;
use App\Http\Controllers\Oiled\ProcessingOilingController;
use App\Http\Controllers\Oiled\ProcessingReceivingController;
use App\Http\Controllers\Oiled\WashController;
use App\Http\Controllers\Oiled\WaterproofingAssessmentController;

Route::prefix('patients/oiled')->name('oiled.')->group(function () {
    Route::get('case-summary', CaseSummaryController::class)->name('summary.edit');

    Route::get('wash', [WashController::class, 'index'])->name('wash.index');
    Route::post('{patient}/wash', [WashController::class, 'store'])->name('wash.store');
    Route::put('{patient}/wash/{wash}', [WashController::class, 'update'])->name('wash.update');
    Route::delete('{patient}/wash/{wash}', [WashController::class, 'destroy'])->name('wash.destroy');

    Route::get('waterproofing-assessment', [WaterproofingAssessmentController::class, 'index'])->name('waterproofing_assessment.index');
    Route::post('{patient}/waterproofing-assessment', [WaterproofingAssessmentController::class, 'store'])->name('waterproofing_assessment.store');
    Route::put('{patient}/waterproofing-assessment/{assessment}', [WaterproofingAssessmentController::class, 'update'])->name('waterproofing_assessment.update');
    Route::delete('{patient}/waterproofing-assessment/{assessment}', [WaterproofingAssessmentController::class, 'destroy'])->name('waterproofing_assessment.destroy');

    Route::name('processing.')->group(function () {
        Route::get('processing', ProcessingController::class)->name('edit');
        Route::put('/{patient}/processing/collection', ProcessingCollectionController::class)->name('collection.update');
        Route::put('/{patient}/processing/receiving', ProcessingReceivingController::class)->name('receiving.update');
        Route::put('/{patient}/processing/evidence', ProcessingEvidenceController::class)->name('evidence.update');
        Route::put('/{patient}/processing/comments', ProcessingCommentsController::class)->name('comments.update');
        Route::put('/{patient}/processing/oiling', ProcessingOilingController::class)->name('oiling.update');
        Route::put('/{patient}/processing/carcass-condition', ProcessingCarcassConditionController::class)->name('carcassCondition.update');
    });
});
