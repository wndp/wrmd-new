<?php

use App\Http\Controllers\Settings\AccountExtensionsController;
use App\Http\Controllers\Settings\AccountProfileController;
use App\Http\Controllers\Settings\ApiController;
use App\Http\Controllers\Settings\ClassificationsController;
use App\Http\Controllers\Settings\DataSharingController;
use App\Http\Controllers\Settings\GeneralSettingsController;
use App\Http\Controllers\Settings\PrivacyController;
use App\Http\Controllers\Settings\ProfilePhotoController;
use App\Http\Controllers\Settings\RestrictRemoteAccessController;
use App\Http\Controllers\Settings\SecurityController;
use App\Http\Controllers\Settings\UsersAuthorizationsController;
use App\Http\Controllers\Settings\UsersController;
use App\Http\Controllers\Settings\VeterinariansController;

Route::prefix('settings')->middleware('can:displaySettings')->group(function () {
    Route::controller(AccountProfileController::class)->group(function () {
        Route::get('account/profile', 'edit')->name('account.profile.edit');
        Route::put('account/profile', 'update')->name('account.profile.update');
        Route::put('account/profile/contact', 'updateContact')->name('account.profile.update.contact');
        Route::put('account/profile/localization', 'updateLocalization')->name('account.profile.update.localization');
    });

    Route::delete('account/profile/profile-photo', [ProfilePhotoController::class, 'destroy'])
        ->name('account.profile-photo.destroy');

    Route::put('users/authorizations/{user}', [UsersAuthorizationsController::class, 'update'])
        ->name('users.authorizations.update');

    Route::resource('users', UsersController::class);

    Route::resource('veterinarians', VeterinariansController::class);

    Route::controller(PrivacyController::class)->group(function () {
        Route::get('people-privacy', 'edit')->name('privacy.edit');
        Route::put('people-privacy', 'update')->name('privacy.update');
    });

    Route::controller(SecurityController::class)->group(function () {
        Route::get('security', 'edit')->name('security.edit');
        Route::put('security', 'update')->name('security.update');
    });
    Route::put('security/remote-access', RestrictRemoteAccessController::class)->name('security.remote-access.update');

    Route::controller(ClassificationsController::class)->group(function () {
        Route::get('classification-tagging', 'edit')->name('classification-tagging.edit');
        Route::put('classification-tagging', 'update')->name('classification-tagging.update');
    });

    Route::controller(GeneralSettingsController::class)->group(function () {
        Route::get('general', 'edit')->name('general-settings.edit');
        Route::put('general', 'update')->name('general-settings.update');
        Route::put('general/treatment-log', 'updateTreatmentLog')->name('general-settings.update.treatment-log');
        Route::put('general/locations', 'updateLocations')->name('general-settings.update.locations');
    });

    Route::controller(AccountExtensionsController::class)->group(function () {
        Route::get('extensions', 'index')->name('extensions.index');
        Route::post('extensions/{namespace}/{account?}', 'store')->name('extensions.store');
        Route::delete('extensions/{namespace}/{account?}', 'destroy')->name('extensions.destroy');
    });

    Route::controller(ApiController::class)->group(function () {
        Route::get('api', 'index')->name('api.index');
        Route::post('api', 'store')->name('api.store');
        Route::delete('api/{token}', 'delete')->name('api.destroy');
    });

    Route::controller(DataSharingController::class)->group(function () {
        Route::get('data-sharing', 'edit')->name('data-sharing.edit');
        Route::put('data-sharing', 'update')->name('data-sharing.update');
    });
});
