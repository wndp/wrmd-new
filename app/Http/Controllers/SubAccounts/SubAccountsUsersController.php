<?php

namespace App\Http\Controllers\SubAccounts;

use App\Exceptions\RecordNotOwned;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SubAccountsUsersController extends Controller
{
    public function __invoke(Team $subAccount)
    {
        abort_unless(
            Auth::user()->currentTeam->hasSubAccount($subAccount),
            new RecordNotOwned()
        );

        $users = $subAccount
            ->allUsers()
            ->load('roles')
            ->filter(fn ($user) => ! Str::startsWith($user->email, 'api-user'))
            ->values()
            ->transform(fn ($user) => [
                ...$user->toArray(),
                'role_name_for_humans' => $user->getRoleNameOnTeamForHumans($subAccount)
            ]);

        return Inertia::render('SubAccounts/Users', compact('subAccount', 'users'));
    }
}
