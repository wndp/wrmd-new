<?php

namespace App\Http\Middleware;

use App\Enums\Plan;
use App\Models\Team;
use App\Repositories\OptionsStore;
use App\Repositories\RecentPatients;
use App\Repositories\SettingsStore;
use App\Support\ExtensionManager;
use App\Support\Wrmd;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $parentShare = parent::share($request);

        $parentShare['environment'] = App::environment();
        $parentShare['appName'] = config('app.name');
        $parentShare['showDonateHeader'] = config('wrmd.show_donate_header');

        if (Auth::check()) {
            $user = $request->user();
            $team = $user->current_team_id ? $user->currentTeam : null;

            $abilities = Collection::make(app(Gate::class)->forUser($user)->abilities())
                ->keys()
                // Abilities computed from methods in App\Policies\*
                ->filter(fn ($ability) => $user->can($ability))
                ->map(fn ($ability) => 'COMPUTED_'.Str::of($ability)->snake()->upper())
                // Abilities assigned through Bouncer
                ->merge($user->getAbilities()->pluck('name'))
                ->sort()
                ->values();

            $parentShare['auth'] = [
                'user' => fn () => array_merge($user->only('id', 'name', 'email'), [
                    'two_factor_enabled' => ! is_null($user->two_factor_secret),
                    'all_teams' => $user->allTeams()->values(),
                    'current_team' => $user->currentTeam
                ]),
                'abilities' => $abilities,
                'team' => $team,
            ];

            $parentShare['notification'] = [
                'heading' => fn () => $request->session()->get('notification.heading'),
                'text' => fn () => $request->session()->get('notification.text'),
                'style' => fn () => $request->session()->get('notification.style'),
            ];

            $parentShare['unreadNotifications'] = $user->unreadNotifications->transform(fn ($notification) => [
                ...$notification->toArray(),
                'created_at_diff' => $notification->created_at->diffForHumans()
            ]);

            if ($team instanceof Team) {
                $parentShare['subscription'] = [
                    'isProPlan' => is_null($team->onGenericTrial()) ? false : $team->sparkPlan()?->name === Plan::PRO->value,
                    'genericTrialEndsAt' => $team->onGenericTrial()
                        ? $team->customer->trial_ends_at->translatedFormat(config('wrmd.date_format'))
                        : null,
                ];

                $parentShare['settings'] = fn () => Wrmd::settings()->all();
                $parentShare['options'] = fn () => OptionsStore::all();
                $parentShare['activatedExtensions'] = fn () => ExtensionManager::getActivated($team)->pluck('extension');
                $parentShare['recentUpdatedPatients'] = fn () => RecentPatients::updated($team);
                $parentShare['recentAdmittedPatients'] = fn () => RecentPatients::admitted($team);
            }
        }

        return $parentShare;
    }
}
