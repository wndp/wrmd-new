<?php

namespace App\Http\Controllers\Settings;

use App\Enums\SettingKey;
use App\Events\TeamUpdated;
use App\Http\Controllers\Controller;
use App\Support\Wrmd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $showTags = (bool) Wrmd::settings(SettingKey::SHOW_TAGS);

        return Inertia::render('Settings/Classifications', compact('showTags'));
    }

    /**
     * Update the resource.
     */
    public function update(Request $request): RedirectResponse
    {
        Wrmd::settings([
            SettingKey::SHOW_TAGS->value => $request->boolean('showTags'),
        ]);

        event(new TeamUpdated(Auth::user()->currentTeam));

        return redirect()->route('classification-tagging.edit')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Classification settings updated.'));
    }
}
