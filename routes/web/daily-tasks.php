<?php

use App\Enums\Ability;
use App\Http\Controllers\DailyTasks\DailyTasksController;
use App\Http\Controllers\DailyTasks\DestroyDailyTaskController;
use App\Http\Controllers\DailyTasks\PastDueTasksController;
use App\Http\Controllers\DailyTasks\RecordedDailyTaskListController;
use App\Http\Controllers\DailyTasks\RecordedPatientDailyTasksController;
use App\Http\Controllers\DailyTasks\ScheduledTasksController;
use Illuminate\Auth\Middleware\Authorize;

Route::middleware(Authorize::using(Ability::VIEW_DAILY_TASKS->value))->group(function () {
    Route::get('daily-tasks', [DailyTasksController::class, 'index'])->name('daily-tasks.index');
    Route::get('patients/daily-tasks', [DailyTasksController::class, 'edit'])->name('patients.daily-tasks.edit');
    Route::get('patients/past-due-tasks', [PastDueTasksController::class, 'edit'])->name('patients.past-due-tasks.edit');
    Route::get('patients/scheduled-tasks', [ScheduledTasksController::class, 'edit'])->name('patients.scheduled-tasks.edit');
});

Route::middleware(Authorize::using(Ability::MANAGE_DAILY_TASKS->value))
    ->delete('daily-tasks/{type}/{id}', DestroyDailyTaskController::class)
    ->name('daily-tasks.delete');

Route::prefix('internal-api')->middleware(Authorize::using(Ability::MANAGE_DAILY_TASKS->value))->group(function () {
    Route::post('daily-tasks/record/patient/{patient}', [RecordedPatientDailyTasksController::class, 'store']);
    Route::delete('daily-tasks/record/patient/{patient}', [RecordedPatientDailyTasksController::class, 'destroy']);
    Route::post('daily-tasks/record/{type}/{id}', [RecordedDailyTaskListController::class, 'store']);
    Route::delete('daily-tasks/record/{type}/{id}', [RecordedDailyTaskListController::class, 'destroy']);
});
