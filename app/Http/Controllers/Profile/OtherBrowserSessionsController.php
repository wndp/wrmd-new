<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtherBrowserSessionsController extends Controller
{
    /**
     * Log out from other browser sessions.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $input = $request->validate([
            'password' => 'required|string|current_password',
        ]);

        Auth::logoutOtherDevices($input['password']);

        return redirect()->route('profile.edit')
            ->with('flash.notificationHeading', 'Success!')
            ->with('flash.notification', __('Your other browser sessions have been logged out.'));
    }
}
