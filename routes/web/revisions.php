<?php

use App\Http\Controllers\PatientBulkRevisionRestorationController;
use App\Http\Controllers\PatientRevisionRestorationController;

Route::middleware('can:displayRevisions')->group(function () {
    Route::put('internal-api/revisions/restore/{revision}/{column?}', PatientRevisionRestorationController::class);
    Route::get('revisions/restore/create', [PatientBulkRevisionRestorationController::class, 'create'])->name('revisions.restore.create');
    Route::put('revisions/restore', [PatientBulkRevisionRestorationController::class, 'bulkUpdate'])->name('revisions.restore.update');
});
