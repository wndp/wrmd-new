<?php

use App\Http\Controllers\Sharing\EmailController;
use App\Http\Controllers\Sharing\ExportController;
use App\Http\Controllers\Sharing\PrintController;
use App\Http\Controllers\Sharing\TransferRequestController;
use App\Http\Controllers\Sharing\TransferResponseController;

Route::middleware('can:share-patients')->group(function () {
    Route::post('share/print/{patient?}', [PrintController::class, 'store'])
        ->name('share.print.store');

    Route::post('share/email/{patient?}', [EmailController::class, 'store'])
        ->name('share.email.store');

    Route::post('share/export/{patient?}', [ExportController::class, 'store'])
        ->name('share.export.store');

    Route::get('patients/transfer', [TransferRequestController::class, 'create'])
        ->name('share.transfer.create');

    Route::post('share/transfer/{patient}', [TransferRequestController::class, 'store'])
        ->name('share.transfer.store');

    Route::post('share/transfer/{transfer:uuid}/accept', [TransferResponseController::class, 'store'])
        ->name('share.transfer.accept');

    Route::delete('share/transfer/{transfer:uuid}/deny', [TransferResponseController::class, 'destroy'])
        ->name('share.transfer.deny');
});
