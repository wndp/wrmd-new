<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\Http\Controllers\Inertia\OtherBrowserSessionsController as JetstreamOtherBrowserSessionsController;

class OtherBrowserSessionsController extends JetstreamOtherBrowserSessionsController
{
    /**
     * Log out from other browser sessions.
     */
    public function __invoke(Request $request, StatefulGuard $guard): RedirectResponse
    {
        $this->destroy($request, $guard);

        return redirect()->route('profile.edit')
            ->with('notification.heading', 'Success!')
            ->with('notification.text', __('Your other browser sessions have been logged out.'));
    }
}
