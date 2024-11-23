<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use App\Events\NoTeams;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChooseTeamController extends Controller
{
    /**
     * Show the form for choosing an team.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usersTeams = Auth::user()->allTeams()
            ->where('status', AccountStatus::ACTIVE)
            ->sortBy('name')
            ->transform(fn ($team) => [
                ...$team->toArray(),
                'locale' => $team->formatted_inline_address
            ])
            ->values();

        if (count($usersTeams) === 0) {
            event(new NoTeams(Auth::user()));

            return Inertia::render('Auth/NoTeams')->with([
                'auth.user' => Auth::user()->only('id', 'name', 'email'),
            ]);
        } elseif (count($usersTeams) === 1) {
            return $this->assignTeam($usersTeams->first());
        }

        return Inertia::render('Auth/ChooseTeam', compact('usersTeams'));
    }

    /**
     * Attempt to choose an team for the authenticated user.
     */
    public function update(Request $request): RedirectResponse
    {
        $usersTeams = Auth::user()
            ->teams
            ->where('status', AccountStatus::ACTIVE)
            ->pluck('id')
            ->implode(',');

        $request->validate([
            'team' => 'required|integer|in:'.$usersTeams,
        ], [
            'team.in' => __('The chosen account is not valid.'),
        ]);

        return $this->assignTeam(Team::findOrFail($request->get('team')));
    }

    /**
     * Assign an team to the user for this session.
     */
    private function assignTeam(Team $team): RedirectResponse
    {
        $result = Auth::user()->switchTeam($team);

        if ($result) {
            return redirect()->route('dashboard')
                ->with('notification.heading', __('Welcome Back!'))
                ->with('notification.text', __('You are now signed into :organizationName.', [
                    'organizationName' => $team->name
                ]));
        }

        // Assume the user is trying to access a team they are not a user of and just bail.
        Auth::guard()->logout();
        session()->flush();
        session()->regenerate();

        return redirect()->route('home');
    }
}
