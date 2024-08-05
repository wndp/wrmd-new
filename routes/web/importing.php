<?php

use App\Http\Controllers\Importing\ConfirmationController;
use App\Http\Controllers\Importing\DeclarationController;
use App\Http\Controllers\Importing\ImportController;
use App\Http\Controllers\Importing\MappingController;
use App\Http\Controllers\Importing\RecapController;
use App\Http\Controllers\Importing\TranslationController;
use App\Http\Controllers\Importing\ValidationController;

Route::prefix('import')->name('import.')->middleware('can:import')->group(function () {
    Route::get('', [DeclarationController::class, 'index'])->name('declaration.index');
    Route::post('', [DeclarationController::class, 'store'])->name('declaration.store');
    Route::get('/map', [MappingController::class, 'index'])->name('map.index');
    Route::post('/map', [MappingController::class, 'store'])->name('map.store');
    Route::get('/translation', [TranslationController::class, 'index'])->name('translation.index');
    Route::post('/translation', [TranslationController::class, 'store'])->name('translation.store');
    Route::get('/confirmation', [ConfirmationController::class, 'index'])->name('confirmation.index');
    Route::get('/validation', [ValidationController::class, 'index'])->name('validation.index');
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');
    Route::get('/recap', [RecapController::class, 'index'])->name('recap.index');
});
