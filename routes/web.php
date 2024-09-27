<?php

use App\Enums\Ability;
use App\Http\Controllers\ChooseTeamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\SelectPatientController;
use App\Http\Controllers\SelectPatientsController;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return Inertia::render('Dashboard');
//     })->name('dashboard');
// });

/** Redirect legacy routes */
Route::redirect('/signin', '/login', Response::HTTP_MOVED_PERMANENTLY);

/* Route to view WRMD API documentation. */
Route::view('api/v3', 'scribe.index')->name('apiv3.index');

require __DIR__.'/web/public.php';

Route::controller(ChooseTeamController::class)->group(function () {
    Route::get('choose-team', 'index')->name('choose_team.index');
    Route::post('choose-team', 'update')->name('choose_team.store');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'app',
    'subscribed',
])->group(function () {

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::controller(NotificationsController::class)->group(function () {
        Route::get('notifications', 'index')->name('notifications.index');
        Route::put('notifications/{notification}', 'update')->name('notifications.update');
        Route::delete('notifications/{notification}', 'destroy')->name('notifications.destroy');
    });

    // /* Select Patient */
    Route::post('select-patient/{patient}', [SelectPatientController::class, 'store'])->name('select-patient.store');
    Route::delete('select-patient/{patient?}', [SelectPatientController::class, 'destroy'])->name('select-patient.destroy');
    Route::post('select-patients', [SelectPatientsController::class, 'store'])->name('select-patients.store');
    Route::delete('select-patients', [SelectPatientsController::class, 'destroy'])->name('select-patients.destroy');

    // Media
    Route::controller(MediaController::class)->middleware(Authorize::using(Ability::MANAGE_ATTACHMENTS->value))->group(function () {
        //Route::get('media', 'index')->name('media.index');
        Route::post('media', 'store')->name('media.store');
        Route::get('media/{media}', 'edit')->name('media.edit');
        Route::put('media/{media}', 'update')->name('media.update');
        Route::delete('media/{media}', 'destroy')->name('media.destroy');
    });

    // require __DIR__.'/web/admin.php';
    // require __DIR__.'/web/sub-accounts.php';
    require __DIR__.'/web/analytics.php';
    require __DIR__.'/web/daily-tasks.php';
    require __DIR__.'/web/forum.php';
    require __DIR__.'/web/hotline.php';
    require __DIR__.'/web/internal-api.php';
    require __DIR__.'/web/maintenance.php';
    require __DIR__.'/web/patient.php';
    require __DIR__.'/web/people.php';
    require __DIR__.'/web/reports.php';
    // require __DIR__.'/web/revisions.php';
    require __DIR__.'/web/searching.php';
    require __DIR__.'/web/profile.php';
    require __DIR__.'/web/settings.php';
    require __DIR__.'/web/sharing.php';
    require __DIR__.'/web/importing.php';
    // require __DIR__.'/web/oiled.php';
    // require __DIR__.'/web/owcn.php';
});
