<?php

namespace App\Http\Controllers\Maintenance;

use App\Events\AccountUpdated;
use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OwcnIoaMaintenanceController extends Controller
{
    /**
     * Display the paper form template settings index page.
     */
    public function index()
    {
        ExtensionNavigation::emit('maintenance');

        $users = Auth::user()->currentAccount->users->where('is_api_user', false);
        $notifyOfIoa = settings()->get('owcn.notifyOfIoa');

        return Inertia::render('Maintenance/OwncIoa', compact('users', 'notifyOfIoa'));
    }

    /**
     * Store paper form template in storage.
     */
    public function update(Request $request)
    {
        settings()->set(
            'owcn.notifyOfIoa',
            is_array($request->notifyOfIoa) ? array_values($request->notifyOfIoa) : []
        );

        event(new AccountUpdated(auth()->user()->currentAccount));

        return redirect()->route('maintenance.owcn_ioa.index')
            ->with('flash.notificationHeading', 'Success!')
            ->with('flash.notification', 'OWCN IOA settings updated.');
    }
}
