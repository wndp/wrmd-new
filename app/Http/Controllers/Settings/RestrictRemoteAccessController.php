<?php

namespace App\Http\Controllers\Settings;

use App\Events\AccountUpdated;
use App\Events\TeamUpdated;
use App\Http\Controllers\Controller;
use App\Support\Wrmd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class RestrictRemoteAccessController extends Controller
{
    /**
     * Update the security settings.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'clinicIp' => 'nullable|required_if:remoteRestricted,true|ip',
        ]);

        $settings = $request->all('remoteRestricted', 'clinicIp', 'roleRemotePermission', 'userRemotePermission');
        $settings[SettingKey::ROLE_REMOTE_PERMISSION] = array_values(Arr::wrap($settings['roleRemotePermission']));
        $settings[SettingKey::USER_REMOTE_PERMISSION] = array_values(Arr::wrap($settings['userRemotePermission']));

        Wrmd::settings($settings);

        event(new TeamUpdated(Auth::user()->currentTeam));

        return redirect()->route('security.edit')
            ->with('notification.heading', __('Success'))
            ->with('notification.text', __('Remote access settings updated.'));
    }
}
