<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdateUserPasswordController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $input = $request->validate([
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => ['required', 'string', Password::min(8)->uncompromised(), 'confirmed'],
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ]);

        Auth::user()->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();

        return redirect()->route('profile.edit')
            ->with('notification.heading', 'Success!')
            ->with('notification.text', __('Your password was updated.'));
    }
}
