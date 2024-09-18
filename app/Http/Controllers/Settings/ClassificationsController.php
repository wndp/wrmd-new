<?php

namespace App\Http\Controllers\Settings;

use App\Events\AccountUpdated;
use App\Events\TeamUpdated;
use App\Http\Controllers\Controller;
use App\Support\Wrmd;
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
        $showTags = (bool) Wrmd::settings('showTags');

        return Inertia::render('Settings/Classifications', compact('showTags'));
    }

    /**
     * Update the resource.
     */
    public function update(): RedirectResponse
    {
        Wrmd::settings(request()->all('showTags'));

        event(new TeamUpdated(Auth::user()->currentAccount));

        return redirect()->route('classification-tagging.edit')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Classification settings updated.'));
    }
}
