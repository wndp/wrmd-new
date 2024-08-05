<?php

namespace App\Http\Controllers\Settings;

use App\Domain\OptionsStore;
use App\Domain\Users\UserOptions;
use App\Events\AccountUpdated;
use App\Http\Controllers\Controller;
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
    public function edit(Request $request, UserOptions $options): Response
    {
        OptionsStore::merge($options);

        $ipAddress = $request->ip();
        $users = Auth::user()->currentAccount->users->where('is_api_user', false)->values();
        $remoteAccess = [
            'remoteRestricted' => (bool) settings('remoteRestricted'),
            'clinicIp' => settings('clinicIp'),
            'userRemotePermission' => settings()->get('userRemotePermission', []),
            'roleRemotePermission' => settings()->get('roleRemotePermission', []),
        ];
        $security = [
            'requireTwoFactor' => (bool) settings()->get('requireTwoFactor'),
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
        settings()->set([
            'requireTwoFactor' => $request->get('requireTwoFactor'),
        ]);

        event(new AccountUpdated(Auth::user()->currentAccount));

        return redirect()->route('security.edit')
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('Security settings have been updated.'));
    }
}
