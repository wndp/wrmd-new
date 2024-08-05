<?php

use App\Http\Controllers\Settings\SubAccountsController;
use App\Http\Controllers\Settings\SubAccountsExtensionsController;
use App\Http\Controllers\Settings\SubAccountsSettingsController;
use App\Http\Controllers\Settings\SubAccountUsersController;

Route::get('sub-accounts', [SubAccountsController::class, 'index'])->name('sub-accounts.index');
Route::get('sub-accounts/create', [SubAccountsController::class, 'create'])->name('sub-accounts.create');
Route::post('sub-accounts', [SubAccountsController::class, 'store'])->name('sub-accounts.store');
Route::get('sub-accounts/{subAccount}', [SubAccountsController::class, 'edit'])->name('sub-accounts.edit');
Route::put('sub-accounts/{subAccount}', [SubAccountsController::class, 'update'])->name('sub-accounts.update');
Route::put('sub-accounts/{subAccount}/settings', [SubAccountsSettingsController::class, 'update'])->name('sub-accounts.settings');

Route::resource('sub-accounts.users', SubAccountUsersController::class);

Route::get('sub-accounts/{sub_account}/extensions/activate/{extension}', [SubAccountsExtensionsController::class, 'store'])->name('sub-accounts.extensions.store');
Route::get('sub-accounts/{sub_account}/extensions/deactivate/{extension}', [SubAccountsExtensionsController::class, 'delete'])->name('sub-accounts.extensions.destroy');
