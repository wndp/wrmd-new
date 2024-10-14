<?php

namespace App\Http\Controllers\Settings;

use App\Events\AccountUpdated;
use App\Events\TeamUpdated;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Wrmd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Silber\Bouncer\BouncerFacade;

class PrivacyController extends Controller
{
    public function __construct()
    {
        Collection::macro('flipFlop', function () {
            $result = $this->keys()->reduce(function ($carry1, $previousKey) {
                $keys = $this->offsetGet($previousKey);

                return array_merge_recursive($carry1, array_reduce($keys, function ($carry2, $newKey) use ($previousKey) {
                    $carry2[$newKey][] = $previousKey;

                    return $carry2;
                }, []));
            }, []);

            return new self($result);
        });
    }

    /**
     * Show the form for editing the privacy settings.
     */
    public function edit(): Response
    {
        $users = Auth::user()->currentTeam->allUsers()->where('is_api_user', false)->values();
        $fullPeopleAccess = (bool) Wrmd::settings(SettingKey::FULL_PEOPLE_ACCESS, 0);
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

        $teammates = Auth::user()->currentTeam->allUsers()->where('is_api_user', false)->values();

        $this->disallowAllPeopleAbilities($teammates);
        $this->allowPeopleAbilities($teammates, $abilities);

        Wrmd::settings([
            SettingKey::FULL_PEOPLE_ACCESS => $request->input('fullPeopleAccess')
        ]);

        event(new TeamUpdated(auth()->user()->currentTeam));
        BouncerFacade::refresh();

        return redirect()->route('privacy.edit')
            ->with('notification.heading', __('Success'))
            ->with('notification.text', __('People privacy settings updated.'));
    }

    /**
     * Disallow all people abilities from all users of the current account.
     */
    private function disallowAllPeopleAbilities($teammates): void
    {
        $teammates->each(function ($user) {
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
    private function allowPeopleAbilities($teammates, array $abilities): void
    {
        $flopped = collect($abilities)
            ->mapWithKeys(function ($emails, $ability) {
                return [Str::kebab($ability) => $emails];
            })
            ->filter()
            ->flipFlop();

        $users = $teammates->whereIn('email', $flopped->keys());

        foreach ($flopped as $userEmail => $abilities) {
            $user = $users->where('email', $userEmail)->first();

            if ($user instanceof User) {
                BouncerFacade::allow($user)->to($abilities);
            }
        }
    }
}
