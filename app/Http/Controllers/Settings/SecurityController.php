<?php

namespace App\Http\Controllers\Settings;

use App\Enums\Role;
use App\Events\AccountUpdated;
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
            'roles' => Options::enumsToSelectable(Role::publicRoles())
        ]);

        $ipAddress = $request->ip();
        $users = Auth::user()->currentTeam->allUsers()->where('is_api_user', false)->values();

        $remoteAccess = [
            'remoteRestricted' => (bool) Wrmd::settings('remoteRestricted'),
            'clinicIp' => Wrmd::settings('clinicIp'),
            'userRemotePermission' => Wrmd::settings()->get('userRemotePermission', []),
            'roleRemotePermission' => Wrmd::settings()->get('roleRemotePermission', []),
        ];
        $security = [
            'requireTwoFactor' => (bool) Wrmd::settings()->get('requireTwoFactor'),
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
            'requireTwoFactor' => $request->get('requireTwoFactor'),
        ]);

        event(new TeamUpdated(Auth::user()->currentTeam));

        return redirect()->route('security.edit')
            ->with('notification.heading', __('Success'))
            ->with('notification.text', __('Security settings have been updated.'));
    }
}
