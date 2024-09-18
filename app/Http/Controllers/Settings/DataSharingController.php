<?php

namespace App\Http\Controllers\Settings;

use App\Events\AccountUpdated;
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
            'wildAlertSharing' => (bool) Wrmd::settings('wildAlertSharing', true),
            'exportSharing' => (bool) Wrmd::settings('exportSharing', true),
        ];

        return Inertia::render('Settings/DataSharing', compact('sharingSettings'));
    }

    /**
     * Update the security settings.
     */
    public function update(Request $request): RedirectResponse
    {
        Wrmd::settings([
            'wildAlertSharing' => $request->get('wildAlertSharing'),
            'exportSharing' => $request->get('exportSharing'),
        ]);

        TeamUpdated::dispatch(Auth::user()->currentTeam);

        return redirect()->route('data-sharing.edit')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Data sharing settings have been updated.'));
    }
}
