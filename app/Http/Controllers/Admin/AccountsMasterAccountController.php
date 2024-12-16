<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class AccountsMasterAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Team $team, Request $request)
    {
        $team->update([
            'master_account_id' => $request->master_account_id,
        ]);

        return back()
            ->with('notification.heading', 'Success!')
            ->with('notification.text', 'Master account updated');
    }
}
