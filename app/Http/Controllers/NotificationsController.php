<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationsController extends Controller
{
    /**
     * Return the authenticated users unread notifications.
     */
    public function index()
    {
        return Inertia::render('Notifications', [
            'notifications' => Auth::user()
                ->currentAccount()
                ->readNotifications()
                ->paginate()
                ->onEachSide(1)
                ->through(function ($notification) {
                    $notification->created_at_for_humans = $notification->created_at->diffForHumans();

                    return $notification;
                }),
        ]);
    }

    /**
     * Mark a notification as being unread.
     */
    public function update(DatabaseNotification $notification)
    {
        abort_unless($notification->notifiable->is(Auth::user()->currentAccount), 404);

        $notification->markAsUnread();

        return back();
    }

    /**
     * Mark a notification as being read.
     */
    public function destroy(DatabaseNotification $notification)
    {
        abort_unless($notification->notifiable->is(Auth::user()->currentAccount), 404);

        return $notification->markAsRead();
    }
}
