<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Users\User;
use App\Events\AccountUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Silber\Bouncer\BouncerFacade;

class PrivacyController extends Controller
{
    /**
     * Show the form for editing the privacy settings.
     */
    public function edit(): Response
    {
        $users = Auth::user()->currentAccount->users->where('is_api_user', false)->values();
        $fullPeopleAccess = (bool) settings('fullPeopleAccess', 0);
        $authorized = $users
            ->each
            ->load('abilities')
            ->pluck('abilities.*.name', 'email')
            ->flipFlop();

        return Inertia::render('Settings/Privacy', compact('users', 'fullPeopleAccess', 'authorized'));
    }

    /**
     * Update the people settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $abilities = $request->all(
            'displayPeople',
            'displayRescuer',
            'searchRescuers',
            'createPeople',
            'deletePeople',
            'exportPeople',
            'combinePeople'
        );

        $accountUsers = Auth::user()->currentAccount->users;
        $this->disallowAllPeopleAbilities($accountUsers);
        $this->allowPeopleAbilities($accountUsers, $abilities);
        settings()->set($request->all('fullPeopleAccess'));

        event(new AccountUpdated(auth()->user()->currentAccount));
        BouncerFacade::refresh();

        return redirect()->route('privacy.edit')
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('People privacy settings updated.'));
    }

    /**
     * Disallow all people abilities from all users of the current account.
     */
    private function disallowAllPeopleAbilities($accountUsers): void
    {
        $accountUsers->each(function ($user) {
            $user->disallow([
                'display-people',
                'display-rescuer',
                'search-rescuers',
                'create-people',
                'delete-people',
                'export-people',
                'combine-people',
            ]);
        });
    }

    /**
     * Assign allowed people abilities to the requested account users.
     */
    private function allowPeopleAbilities($accountUsers, array $abilities): void
    {
        $flopped = collect($abilities)
            ->mapWithKeys(function ($emails, $ability) {
                return [Str::kebab($ability) => $emails];
            })
            ->filter()
            ->flipFlop();

        $users = $accountUsers->whereIn('email', $flopped->keys());

        foreach ($flopped as $userEmail => $abilities) {
            $user = $users->where('email', $userEmail)->first();

            if ($user instanceof User) {
                BouncerFacade::allow($user)->to($abilities);
            }
        }
    }
}
