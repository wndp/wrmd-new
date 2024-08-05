<?php

use App\Http\Controllers\Forum\ForumGroupExitController;
use App\Http\Controllers\Forum\ForumGroupsController;
use App\Http\Controllers\Forum\RepliesController;
use App\Http\Controllers\Forum\ThreadIssuesController;
use App\Http\Controllers\Forum\ThreadsController;
use App\Http\Controllers\Forum\ThreadStatusController;
use App\Http\Controllers\Forum\ThreadSubscriptionsController;

Route::redirect('/threads/{thread}', '/forum/{thread}');

Route::prefix('forum')->group(function () {
    Route::post('group', [ForumGroupsController::class, 'store'])
        ->name('forum.group.store');

    Route::put('group/{group}', [ForumGroupsController::class, 'update'])
        ->name('forum.group.update');

    Route::delete('group/{group}', [ForumGroupsController::class, 'destroy'])->name('forum.group.destroy');

    Route::delete('group/{group}/exit', ForumGroupExitController::class)->name('forum.group_exit.destroy');

    Route::get('', [ThreadsController::class, 'index'])->name('forum.index');

    Route::get('{thread}', [ThreadsController::class, 'show'])->name('forum.show');

    Route::post('', [ThreadsController::class, 'store'])->name('forum.store');

    Route::put('{thread}/status', ThreadStatusController::class);

    Route::post('{thread}/subscriptions', [ThreadSubscriptionsController::class, 'store']);

    Route::delete('{thread}/subscriptions', [ThreadSubscriptionsController::class, 'destroy']);

    Route::get('{thread}/issues', [ThreadIssuesController::class, 'show']);

    Route::post('{thread}/issues', [ThreadIssuesController::class, 'store']);

    Route::delete('{thread}/issues', [ThreadIssuesController::class, 'destroy']);

    Route::post('{thread}/replies', [RepliesController::class, 'store'])
        ->name('forum.replies.store');
});
