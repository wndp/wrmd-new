<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AccountSpoofController extends Controller
{
    /**
     * Spoof an account
     */
    public function __invoke(Team $team): RedirectResponse
    {
        Auth::user()->joinTeam($team, Role::ADMIN);
        Auth::user()->switchTeam($team);

        session()->put('isSpoofing', true);

        return redirect()->route('dashboard')
            ->with('notification.heading', 'Signed In!')
            ->with('notification.text', "You are now signed into $team->name.");
    }
}
