<?php

namespace App\Http\Controllers\Settings;

use App\Enums\SettingKey;
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
use Laravel\Paddle\Cashier;

class AccountProfileController extends Controller
{
    public function edit(): Response
    {
        OptionsStore::add([
            new LocaleOptions,
        ]);

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

        if ($team->wasChanged('name') && $customer = $team->customer) {
            retry(5, fn () => Cashier::api('PATCH', "customers/{$customer->paddle_id}", [
                'name' => $request->input('name'),
            ]), 100);
        }

        return redirect()->route('account.profile.edit')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your account profile was updated.'));
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

        if ($team->wasChanged('contact_email') && $customer = $team->customer) {
            retry(5, fn () => Cashier::api('PATCH', "customers/{$customer->paddle_id}", [
                'email' => $request->input('contact_email'),
            ]), 100);
        }

        return redirect()->route('account.profile.edit')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your account contact details were updated.'));
    }

    /**
     * Update the current accounts localization details.
     */
    public function updateLocalization(Request $request): RedirectResponse
    {
        $request->validate([
            'timezone' => 'required|timezone',
            'language' => 'required',
        ]);

        Wrmd::settings([
            SettingKey::TIMEZONE->value => $request->input('timezone'),
            SettingKey::LANGUAGE->value => $request->input('language'),
        ]);

        app()->setLocale($request->language);

        // if ($customer = Auth::user()->currentTeam->customer) {
        //     retry(5, fn () => Cashier::api('PATCH', "customers/{$customer->paddle_id}", [
        //         'locale' => $request->input('language'),
        //     ]), 100);
        // }

        //Cache::forget('timezone.'.Auth::id());

        return redirect()->route('account.profile.edit')->cookie(
            'locale',
            $request->language,
            2628000
        )
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your account localization was updated.'));
    }
}
