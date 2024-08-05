<?php

namespace App\Http\Controllers\Settings;

use App\Events\AccountUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DataSharingController extends Controller
{
    /**
     * Show the form for editing the security settings.
     */
    public function edit(): Response
    {
        $sharingSettings = [
            'wildAlertSharing' => (bool) settings()->get('wildAlertSharing', true),
            'exportSharing' => (bool) settings()->get('exportSharing', true),
        ];

        return Inertia::render('Settings/DataSharing', compact('sharingSettings'));
    }

    /**
     * Update the security settings.
     */
    public function update(Request $request): RedirectResponse
    {
        settings()->set([
            'wildAlertSharing' => $request->get('wildAlertSharing'),
            'exportSharing' => $request->get('exportSharing'),
        ]);

        event(new AccountUpdated(Auth::user()->currentAccount));

        return redirect()->route('data-sharing.edit')
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('Data sharing settings have been updated.'));
    }
}
