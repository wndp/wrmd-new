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
use Inertia\Inertia;
use Inertia\Response;

class SecurityController extends Controller
{
    /**
     * Show the form for editing the security settings.
     */
    public function edit(Request $request): Response
    {
        OptionsStore::add([
            'roles' => Options::enumsToSelectable(Role::publicRoles()),
        ]);

        $ipAddress = $request->ip();
        $users = Auth::user()->currentTeam->allUsers()->where('is_api_user', false)->values();

        $remoteAccess = [
            'remoteRestricted' => (bool) Wrmd::settings(SettingKey::REMOTE_RESTRICTED),
            'clinicIp' => Wrmd::settings(SettingKey::CLINIC_IP),
            'userRemotePermission' => Wrmd::settings()->get(SettingKey::USER_REMOTE_PERMISSION, []),
            'roleRemotePermission' => Wrmd::settings()->get(SettingKey::ROLE_REMOTE_PERMISSION, []),
        ];
        $security = [
            'requireTwoFactor' => (bool) Wrmd::settings()->get(SettingKey::REQUIRE_TWO_FACTOR),
        ];

        return Inertia::render('Settings/Security', compact(
            'ipAddress',
            'users',
            'remoteAccess',
            'security'
        ));
    }

    /**
     * Update the security settings.
     */
    public function update(Request $request): RedirectResponse
    {
        Wrmd::settings([
            SettingKey::REQUIRE_TWO_FACTOR => $request->get('requireTwoFactor'),
        ]);

        event(new TeamUpdated(Auth::user()->currentTeam));

        return redirect()->route('security.edit')
            ->with('notification.heading', __('Success'))
            ->with('notification.text', __('Security settings have been updated.'));
    }
}
