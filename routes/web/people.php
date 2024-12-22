<?php

use App\Http\Controllers\People\Combine\CombineMatchesController;
use App\Http\Controllers\People\Combine\CombineMergeController;
use App\Http\Controllers\People\Combine\CombineReviewController;
use App\Http\Controllers\People\Combine\CombineSearchController;
use App\Http\Controllers\People\PersonController;
use App\Http\Controllers\People\PersonDonationsController;
use App\Http\Controllers\People\PersonExportController;
use App\Http\Controllers\People\PersonIncidentsController;
use App\Http\Controllers\People\PersonPatientsController;
use Illuminate\Auth\Middleware\Authorize;

/**
 * Authorization abilities are determined from methods in \App\Policies\PrivacyPolicy
 */
Route::middleware(Authorize::using('viewPeople'))->group(function () {
    Route::get('people/rescuers', [PersonController::class, 'index'])
        ->name('people.rescuers.index');

    Route::get('people/reporting-parties', [PersonController::class, 'index'])
        ->name('people.reporting-parties.index');

    Route::get('people/volunteers', [PersonController::class, 'index'])
        ->name('people.volunteers.index');

    Route::get('people/members', [PersonController::class, 'index'])
        ->name('people.members.index');

    Route::get('people/donors', [PersonController::class, 'index'])
        ->name('people.donors.index');

    Route::middleware(Authorize::using('createPeople'))
        ->get('people/create', [PersonController::class, 'create'])
        ->name('people.create');

    Route::middleware(Authorize::using('exportPeople'))
        ->post('people/export', PersonExportController::class)
        ->name('people.export');

    Route::middleware(Authorize::using('createPeople'))
        ->post('people', [PersonController::class, 'store'])
        ->name('people.store');

    Route::get('people/{person}/profile', [PersonController::class, 'edit'])
        ->name('people.edit');

    Route::put('people/{person}', [PersonController::class, 'update'])
        ->name('people.update');

    Route::delete('people/{person}', [PersonController::class, 'destroy'])
        ->name('people.destroy');

    Route::middleware(Authorize::using('combinePeople'))->group(function () {
        Route::get('people/combine', CombineSearchController::class)
            ->name('people.combine.search');

        Route::get('people/combine/matches', CombineMatchesController::class)
            ->name('people.combine.matches');

        Route::get('people/combine/review/{person}', CombineReviewController::class)
            ->name('people.combine.review');

        Route::post('people/combine/merge/', CombineMergeController::class)
            ->name('people.combine.merge');
    });

    Route::scopeBindings()->group(function () {
        Route::get('people/{person}/donations', [PersonDonationsController::class, 'index'])
            ->name('people.donations.index');

        Route::post('people/{person}/donations', [PersonDonationsController::class, 'store'])
            ->name('people.donations.store');

        Route::put('people/{person}/donations/{donation}', [PersonDonationsController::class, 'update'])
            ->name('people.donations.update');

        Route::delete('people/{person}/donations/{donation}', [PersonDonationsController::class, 'destroy'])
            ->name('people.donations.destroy');
    });

    Route::get('people/{person}/patients', PersonPatientsController::class)
        ->name('people.patients.index');

    Route::get('people/{person}/hotline', PersonIncidentsController::class)
        ->name('people.hotline.index');
});
