<?php

use App\Http\Controllers\SubAccounts\SubAccountsController;
use App\Http\Controllers\SubAccounts\SubAccountsExtensionsController;
use App\Http\Controllers\SubAccounts\SubAccountsSettingsController;
use App\Http\Controllers\SubAccounts\SubAccountsUsersController;

Route::prefix('sub-accounts')->name('sub_accounts.')->middleware('can:viewSubAccounts')->group(function () {
    Route::controller(SubAccountsController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('', 'store')->name('store');
        Route::get('/{subAccount}', 'show')->name('show');
        Route::get('/{subAccount}/edit', 'edit')->name('edit');
        Route::put('/{subAccount}', 'update')->name('update');
    });
    Route::controller(SubAccountsSettingsController::class)->group(function () {
        Route::get('/{subAccount}/settings', 'edit')->name('settings.edit');
        Route::put('/{subAccount}/settings', 'update')->name('settings.update');
    });
    Route::get('/{subAccount}/users', SubAccountsUsersController::class)->name('users.index');
    Route::get('/{subAccount}/extensions', SubAccountsExtensionsController::class)->name('extensions.edit');
});
