<?php

namespace App\Http\Controllers\Settings;

use App\Enums\Role;
use App\Enums\SettingKey;
use App\Events\TeamUpdated;
use App\Http\Controllers\Controller;
use App\Options\Options;
use App\Repositories\OptionsStore;
use App\Support\Wrmd;
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
    public function edit()
    {
        OptionsStore::add([
            'roles' => Options::enumsToSelectable(Role::publicRoles()),
        ]);

        $generalSettings = [
            'logOrder' => Wrmd::settings(SettingKey::LOG_ORDER, 'desc'),
            'logAllowAuthorEdit' => (bool) Wrmd::settings(SettingKey::LOG_ALLOW_AUTHOR_EDIT),
            'logAllowEdit' => Wrmd::settings(SettingKey::LOG_ALLOW_EDIT, []),
            'logAllowDelete' => Wrmd::settings(SettingKey::LOG_ALLOW_DELETE, []),
            'logShares' => (bool) Wrmd::settings(SettingKey::LOG_SHARES),
            'showLookupRescuer' => (bool) Wrmd::settings(SettingKey::SHOW_LOOKUP_RESCUER),
            'showGeolocationFields' => (bool) Wrmd::settings(SettingKey::SHOW_GEOLOCATION_FIELDS),
            'areas' => implode(', ', Wrmd::settings(SettingKey::AREAS, [])),
            'enclosures' => implode(', ', Wrmd::settings(SettingKey::ENCLOSURES, [])),
        ];

        // $klist = array_flip(Wrmd::settings('listFields', config('wrmd.defaultListFields')));
        // $fields = fields()->filterOut('selectable')->getLabels()->diffKeys(
        //     array_flip(config('wrmd.always_list_fields'))
        // );

        // $listedFields = collect(array_replace($klist, $fields->toArray()))
        //     ->intersectByKeys($klist)
        //     ->transform(function ($label, $value) {
        //         return compact('label', 'value');
        //     })
        //     ->values();

        // $selectableFields = $fields->transform(function ($label, $value) {
        //     return compact('label', 'value');
        // })
        //     // ->groupBytable(true)
        //     // ->transform(function ($fields, $table) {
        //     //     return [
        //     //         'label' => $table,
        //     //         'options' => $fields->transform(function ($label, $value) {
        //     //             return compact('label', 'value');
        //     //         })->values()
        //     //     ];
        //     // })
        //     ->values();

        $selectableFields = $listedFields = [];

        return Inertia::render('Settings/GeneralSettings', compact('generalSettings', 'selectableFields', 'listedFields'));
    }

    /**
     * Update the generic settings in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        Wrmd::settings([
            SettingKey::SHOW_LOOKUP_RESCUER->value => $request->boolean('showLookupRescuer'),
            SettingKey::SHOW_GEOLOCATION_FIELDS->value => $request->boolean('showGeolocationFields'),
            SettingKey::LIST_FIELDS->value => $request->get('listFields'),
        ]);

        event(new TeamUpdated(Auth::user()->currentTeam));

        return redirect()->route('general-settings.edit')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Generic settings have been updated.'));
    }

    /**
     * Update the treatment log settings in storage.
     */
    public function updateCareLog(Request $request): RedirectResponse
    {
        Wrmd::settings([
            SettingKey::LOG_ORDER->value => $request->get('logOrder'),
            SettingKey::LOG_ALLOW_AUTHOR_EDIT->value => $request->boolean('logAllowAuthorEdit'),
            SettingKey::LOG_ALLOW_EDIT->value => $request->boolean('logAllowEdit'),
            SettingKey::LOG_ALLOW_DELETE->value => $request->boolean('logAllowDelete'),
            SettingKey::LOG_SHARES->value => $request->boolean('logShares'),
        ]);

        event(new TeamUpdated(Auth::user()->currentTeam));

        return redirect()->route('general-settings.edit')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Treatment log settings have been updated.'));
    }

    /**
     * Update the locations settings in storage.
     */
    public function updateLocations(Request $request): RedirectResponse
    {
        Wrmd::settings([
            SettingKey::AREAS => Str::of($request->get('areas'))->explode(',')->map(fn ($s) => trim($s)),
            SettingKey::ENCLOSURES => Str::of($request->get('enclosures'))->explode(',')->map(fn ($s) => trim($s)),
        ]);

        event(new TeamUpdated(Auth::user()->currentTeam));

        return redirect()->route('general-settings.edit')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Location settings have been updated.'));
    }
}
