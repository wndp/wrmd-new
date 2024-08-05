<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Options\LocaleOptions;
use App\Repositories\OptionsStore;
use App\Support\Wrmd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class AccountProfileController extends Controller
{
    public function edit(LocaleOptions $options): Response
    {
        OptionsStore::merge($options);

        return Inertia::render('Settings/AccountProfile');
    }

    /**
     * Update the current accounts profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $team = Auth::user()->currentTeam;

        $team->update($request->validate([
            'name' => 'required',
            'country' => 'required',
            'subdivision' => 'required',
            'city' => 'required',
            'address' => 'required',
            'postal_code' => 'nullable',
            'federal_permit_number' => 'nullable',
            'subdivision_permit_number' => 'nullable',
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]));

        if ($request->has('photo')) {
            $team->updateProfilePhoto(
                $request->file('photo')
            );
        }

        return redirect()->route('account.profile.edit')
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('Your account profile was updated.'));
    }

    /**
     * Update the current accounts contact details.
     */
    public function updateContact(Request $request): RedirectResponse
    {
        $team = Auth::user()->currentTeam;

        $team->update($request->validate([
            'contact_name' => 'required',
            'phone_number' => 'required',
            'contact_email' => 'required|email',
            'website' => 'nullable',
        ]));

        return redirect()->route('account.profile.edit')
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('Your account contact details were updated.'));
    }

    /**
     * Update the current accounts localization details.
     */
    public function updateLocalization(Request $request): RedirectResponse
    {
        Wrmd::settings($request->validate([
            'timezone' => 'required|timezone',
            'language' => 'required',
        ]));

        app()->setLocale($request->language);

        Cache::forget('timezone.'.Auth::id());

        return redirect()->route('account.profile.edit')->cookie(
            'locale',
            $request->language,
            2628000
        )
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('Your account localization was updated.'));
    }
}
