<?php

namespace App\Http\Controllers\Settings;

use App\Events\AccountUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ClassificationsController extends Controller
{
    /**
     * Show the form for editing the security settings.
     */
    public function edit(): Response
    {
        $showTags = (bool) settings('showTags');

        return Inertia::render('Settings/Classifications', compact('showTags'));
    }

    /**
     * Update the resource.
     */
    public function update(): RedirectResponse
    {
        settings()->set(request()->all('showTags'));

        event(new AccountUpdated(Auth::user()->currentAccount));

        return redirect()->route('classification-tagging.edit')
            ->with('flash.notificationHeading', 'Success!')
            ->with('flash.notification', 'Classification settings updated.');
    }
}
