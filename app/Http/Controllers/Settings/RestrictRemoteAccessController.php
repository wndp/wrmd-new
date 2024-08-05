<?php

namespace App\Http\Controllers\Settings;

use App\Events\AccountUpdated;
use App\Http\Controllers\Controller;
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
        $this->validate($request, [
            'clinicIp' => 'nullable|required_with:remoteRestricted|ip',
        ]);

        $settings = $request->all('remoteRestricted', 'clinicIp', 'roleRemotePermission', 'userRemotePermission');
        $settings['roleRemotePermission'] = array_values(Arr::wrap($settings['roleRemotePermission']));
        $settings['userRemotePermission'] = array_values(Arr::wrap($settings['userRemotePermission']));

        settings()->set($settings);

        event(new AccountUpdated(Auth::user()->currentAccount));

        return redirect()->route('security.edit')
            ->with('flash.notificationHeading', 'Success')
            ->with('flash.notification', 'Remote access settings updated.');
    }
}
