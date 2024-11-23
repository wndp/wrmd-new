<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Inertia\Inertia;

class AccountsUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Team $team)
    {
        $users = $team->allUsers()->transform(fn ($user) => [
            ...$user->toArray(),
            'created_at_diff_for_humans' => $user->created_at->translatedFormat(config('wrmd.date_format')),
            'role_name_for_humans' => $user->getRoleNameOnTeamForHumans($team)
        ]);

        return Inertia::render('Admin/Teams/Users', compact('team', 'users'));
    }
}
