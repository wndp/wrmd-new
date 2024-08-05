<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Database\RecordNotOwnedResponse;
use App\Domain\Users\User;
use App\Http\Controllers\Controller;
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
        abort_unless($user->inAccount(Auth::user()->currentAccount), new RecordNotOwnedResponse());

        $data = $request->validate([
            'userAbilities' => 'required|array',
        ]);

        $userAbilities = collect($data['userAbilities'])
            ->partition(fn ($allowance, $ability) => $allowance === 'allowed');

        BouncerFacade::sync($user)->abilities($userAbilities->first()->keys()->toArray());
        BouncerFacade::sync($user)->forbiddenAbilities($userAbilities->last()->keys()->toArray());
        BouncerFacade::refreshFor($user);

        return redirect()->route('users.edit', $user)
            ->with('flash.notificationHeading', 'Authorizations Updated')
            ->with('flash.notification', "Updated {$user->name}'s authorizations.");
    }
}
