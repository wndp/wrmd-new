<?php

use App\Http\Controllers\Profile\OtherBrowserSessionsController;
use App\Http\Controllers\Profile\UpdateUserPasswordController;
use App\Http\Controllers\Profile\UserProfileController;
use App\Http\Middleware\RedirectIfTwoFactorRequired;

Route::withoutMiddleware(RedirectIfTwoFactorRequired::class)
    ->prefix('user/profile')
    ->name('profile.')
    ->group(function () {
        Route::get('', [UserProfileController::class, 'edit'])->name('edit');
        Route::put('', [UserProfileController::class, 'update'])->name('update');
        Route::put('password', UpdateUserPasswordController::class)->name('update.password');
        Route::delete('other-browser-sessions', OtherBrowserSessionsController::class)->name('other-browser-sessions.destroy');
        Route::delete('', [UserProfileController::class, 'destroy'])->name('current-user.destroy');
    });
