<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', ['confirmsTwoFactorAuthentication' => true]);
    }

    public function update(Request $request): RedirectResponse
    {
        Auth::user()->update($request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]));

        return redirect()->route('profile.edit')
            ->with('flash.notificationHeading', 'Success!')
            ->with('flash.notification', __('Your profile was updated.'));
    }

    /**
     * Delete the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, StatefulGuard $guard)
    {
        $request->validate([
            'password' => 'required|string|current_password',
        ]);

        // Call a job that deletes the user and related data.
        // The job may do things like:
        // $user->deleteProfilePhoto();
        // $user->tokens->each->delete();
        // $user->delete();

        $guard->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Inertia::location(url('/'));
    }
}
