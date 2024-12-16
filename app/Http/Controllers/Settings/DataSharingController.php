<?php

namespace App\Http\Controllers\Settings;

use App\Enums\SettingKey;
use App\Http\Controllers\Controller;
use App\Support\Wrmd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Jetstream\Events\TeamUpdated;

class DataSharingController extends Controller
{
    /**
     * Show the form for editing the security settings.
     */
    public function edit(): Response
    {
        $sharingSettings = [
            'wildAlertSharing' => (bool) Wrmd::settings(SettingKey::WILD_ALERT_SHARING, true),
            'exportSharing' => (bool) Wrmd::settings(SettingKey::EXPORT_SHARING, true),
        ];

        return Inertia::render('Settings/DataSharing', compact('sharingSettings'));
    }

    /**
     * Update the security settings.
     */
    public function update(Request $request): RedirectResponse
    {
        Wrmd::settings([
            SettingKey::WILD_ALERT_SHARING => $request->get('wildAlertSharing'),
            SettingKey::EXPORT_SHARING => $request->get('exportSharing'),
        ]);

        TeamUpdated::dispatch(Auth::user()->currentTeam);

        return redirect()->route('data-sharing.edit')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Data sharing settings have been updated.'));
    }
}
