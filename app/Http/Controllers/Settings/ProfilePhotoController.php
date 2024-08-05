<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProfilePhotoController extends Controller
{
    /**
     * Delete the current user's profile photo.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->currentAccount()->deleteProfilePhoto();

        return redirect()->route('account.profile.edit')->with('status', 'profile-photo-deleted');
    }
}
