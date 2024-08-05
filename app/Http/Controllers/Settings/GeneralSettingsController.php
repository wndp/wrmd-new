<?php

namespace App\Http\Controllers\Settings;

use App\Domain\OptionsStore;
use App\Domain\Users\UserOptions;
use App\Events\AccountUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GeneralSettingsController extends Controller
{
    /**
     * Show the form for editing the security settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(UserOptions $options)
    {
        OptionsStore::merge($options);

        $generalSettings = [
            'logOrder' => settings()->get('logOrder', 'desc'),
            'logAllowAuthorEdit' => (bool) settings()->get('logAllowAuthorEdit'),
            'logAllowEdit' => settings()->get('logAllowEdit', []),
            'logAllowDelete' => settings()->get('logAllowDelete', []),
            'logShares' => (bool) settings()->get('logShares'),
            'showLookupRescuer' => (bool) settings()->get('showLookupRescuer'),
            'showGeolocationFields' => (bool) settings()->get('showGeolocationFields'),
            'areas' => implode(', ', settings()->get('areas', [])),
            'enclosures' => implode(', ', settings()->get('enclosures', [])),
        ];

        $klist = array_flip(settings()->get('listFields', config('wrmd.defaultListFields')));
        $fields = fields()->filterOut('selectable')->getLabels()->diffKeys(
            array_flip(config('wrmd.alwaysListFields'))
        );

        $listedFields = collect(array_replace($klist, $fields->toArray()))
            ->intersectByKeys($klist)
            ->transform(function ($label, $value) {
                return compact('label', 'value');
            })
            ->values();

        $selectableFields = $fields->transform(function ($label, $value) {
            return compact('label', 'value');
        })
            // ->groupBytable(true)
            // ->transform(function ($fields, $table) {
            //     return [
            //         'label' => $table,
            //         'options' => $fields->transform(function ($label, $value) {
            //             return compact('label', 'value');
            //         })->values()
            //     ];
            // })
            ->values();

        return Inertia::render('Settings/GeneralSettings', compact('generalSettings', 'selectableFields', 'listedFields'));
    }

    /**
     * Update the generic settings in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        settings()->set([
            'showLookupRescuer' => $request->get('showLookupRescuer'),
            'showGeolocationFields' => $request->get('showGeolocationFields'),
            'listFields' => $request->get('listFields'),
        ]);

        event(new AccountUpdated(Auth::user()->currentAccount));

        return redirect()->route('general-settings.edit')
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('Generic settings have been updated.'));
    }

    /**
     * Update the treatment log settings in storage.
     */
    public function updateTreatmentLog(Request $request): RedirectResponse
    {
        settings()->set([
            'logOrder' => $request->get('logOrder'),
            'logAllowAuthorEdit' => $request->get('logAllowAuthorEdit'),
            'logAllowEdit' => $request->get('logAllowEdit'),
            'logAllowDelete' => $request->get('logAllowDelete'),
            'logShares' => $request->get('logShares'),
        ]);

        event(new AccountUpdated(Auth::user()->currentAccount));

        return redirect()->route('general-settings.edit')
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('Treatment log settings have been updated.'));
    }

    /**
     * Update the locations settings in storage.
     */
    public function updateLocations(Request $request): RedirectResponse
    {
        settings()->set([
            'areas' => Str::of($request->get('areas'))->explode(',')->map(fn ($s) => trim($s)),
            'enclosures' => Str::of($request->get('enclosures'))->explode(',')->map(fn ($s) => trim($s)),
        ]);

        event(new AccountUpdated(Auth::user()->currentAccount));

        return redirect()->route('general-settings.edit')
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('Location settings have been updated.'));
    }
}
