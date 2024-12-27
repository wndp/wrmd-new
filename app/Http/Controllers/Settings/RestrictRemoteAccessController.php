<?php

namespace App\Http\Controllers\Settings;

use App\Enums\SettingKey;
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
            'clinicIp' => 'nullable|required_if_accepted:remoteRestricted|ip',
        ]);

        Wrmd::settings([
            SettingKey::REMOTE_RESTRICTED->value => $request->boolean('remoteRestricted'),
            SettingKey::CLINIC_IP->value => $request->input('clinicIp'),
            SettingKey::ROLE_REMOTE_PERMISSION->value => array_values(Arr::wrap($request->input('roleRemotePermission'))),
            SettingKey::USER_REMOTE_PERMISSION->value => array_values(Arr::wrap($request->input('userRemotePermission'))),
        ]);

        event(new TeamUpdated(Auth::user()->currentTeam));

        return redirect()->route('security.edit')
            ->with('notification.heading', __('Success'))
            ->with('notification.text', __('Remote access settings updated.'));
    }
}
