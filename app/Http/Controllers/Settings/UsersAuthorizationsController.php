<?php

namespace App\Http\Controllers\Settings;

use App\Exceptions\RecordNotOwned;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Silber\Bouncer\BouncerFacade;

class UsersAuthorizationsController extends Controller
{
    /**
     * Update an account users authorizations in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->belongsToTeam(Auth::user()->currentTeam), new RecordNotOwned);

        $data = $request->validate([
            'userAbilities' => 'required|array',
        ]);

        $userAbilities = collect($data['userAbilities'])
            ->partition(fn ($allowance, $ability) => $allowance === 'allowed');

        BouncerFacade::sync($user)->abilities($userAbilities->first()->keys()->toArray());
        BouncerFacade::sync($user)->forbiddenAbilities($userAbilities->last()->keys()->toArray());
        BouncerFacade::refreshFor($user);

        return redirect()->route('users.edit', $user)
            ->with('notification.heading', 'Authorizations Updated')
            ->with('notification.text', "Updated {$user->name}'s authorizations.");
    }
}
