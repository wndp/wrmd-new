<?php

use App\Http\Controllers\Hotline\DeletedIncidentController;
use App\Http\Controllers\Hotline\HotlineSearchController;
use App\Http\Controllers\Hotline\IncidentAttachmentsController;
use App\Http\Controllers\Hotline\IncidentCommunicationsController;
use App\Http\Controllers\Hotline\IncidentController;
use App\Http\Controllers\Hotline\IncidentDescriptionController;
use App\Http\Controllers\Hotline\IncidentMapController;
use App\Http\Controllers\Hotline\IncidentPatientController;
use App\Http\Controllers\Hotline\IncidentReportingPartyController;
use App\Http\Controllers\Hotline\IncidentResolutionController;

//Route::redirect('', 'hotline/open', 301)->name('open.index');

Route::prefix('hotline')->name('hotline.')->scopeBindings()->group(function () {
    Route::controller(IncidentController::class)->middleware('can:display-hotline')->group(function () {
        Route::get('/open', 'index')->name('open.index');
        Route::get('/resolved', 'index')->name('resolved.index');
        Route::get('/unresolved', 'index')->name('unresolved.index');
        Route::get('/deleted', 'index')->name('deleted.index');
    });
    Route::controller(IncidentController::class)->middleware('can:manage-hotline')->group(function () {
        Route::get('{incident}/edit', 'edit')->name('incident.edit');
        Route::get('/create', 'create')->name('incident.create');
        Route::post('', 'store')->name('incident.store');
        Route::put('/{incident}', 'update')->name('incident.update');
        Route::delete('/{incident}', 'destroy')->name('incident.destroy');
    });
    Route::middleware('can:manage-hotline')->group(function () {
        Route::put('/{incident}/description', IncidentDescriptionController::class)->name('incident.update.description');
        Route::put('/{incident}/resolution', IncidentResolutionController::class)->name('incident.update.resolution');
    });
    Route::controller(IncidentCommunicationsController::class)->middleware('can:manage-hotline')->group(function () {
        Route::get('{incident}/communications', 'index')->name('incident.communications.index');
        Route::post('hotline/{incident}/communications', 'store')->name('incident.communications.store');
        Route::put('hotline/{incident}/communications/{communication}', 'update')->name('incident.communications.update');
        Route::delete('hotline/{incident}/communications/{communication}', 'destroy')->name('incident.communications.destroy');
    });
    Route::get('{incident}/reporting-party', IncidentReportingPartyController::class)->name('incident.reporting_party');
    Route::get('{incident}/attachments', IncidentAttachmentsController::class)->name('incident.attachments');
    Route::get('{incident}/map', IncidentMapController::class)->name('incident.map');
    Route::delete('deleted/{incident}', [DeletedIncidentController::class, 'destroy'])
        ->name('deleted.destroy')
        ->middleware('can:manage-hotline');
    Route::controller(HotlineSearchController::class)->middleware('can:display-hotline')->group(function () {
        Route::get('search', 'create')->name('search.create');
        Route::post('search', 'search')->name('search.search');
    });
    Route::post('{incident}/patient', IncidentPatientController::class)->name('incident.patient.store');
});
