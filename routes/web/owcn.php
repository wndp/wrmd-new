<?php

use App\Http\Controllers\Owcn\OwcnRedirectController;
use App\Http\Controllers\Owcn\WrappController;

Route::prefix('owcn')->name('owcn.')->group(function () {
    Route::get('/', OwcnRedirectController::class)->name('index');
    Route::get('/wrapp', WrappController::class)->name('wrapp.index');
});
