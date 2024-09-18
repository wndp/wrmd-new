<?php

namespace App\Http\Middleware;

use App\Repositories\OptionsStore;
use App\Repositories\RecentPatients;
use App\Repositories\SettingsStore;
use App\Support\ExtensionNavigation;
use App\Support\Wrmd;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\Request;
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
        $abilities = $request->user() ? collect(
            app(Gate::class)->forUser($request->user())->abilities()
        )
            ->keys()
            ->filter(fn ($ability) => $request->user()->can($ability))
            ->map(fn ($ability) => 'COMPUTED_'.Str::of($ability)->snake()->upper())
            ->when($request->user()->can('ANYTHING'), fn ($abilities) => $abilities->push('ANYTHING'))
            ->merge($request->user()->getAbilities()->pluck('name'))
            ->sort()
            ->values()
            : [];

        $team = $request->user()?->current_team_id
            ? $request->user()->currentTeam
            : null;

        return [
            ... parent::share($request),
            'appName' => config('app.name'),
            'showDonateHeader' => config('wrmd.donateHeader'),
            'auth' => [
                'user' => fn () => $request->user()
                    ? array_merge($request->user()->only('id', 'name', 'email'), [
                        'two_factor_enabled' => ! is_null($request->user()?->two_factor_secret),
                        'all_teams' => $request->user()->allTeams()->values(),
                        'current_team' => $request->user()->currentTeam
                    ])
                    : null,
                'abilities' => $abilities,
                'team' => fn () => $team
                    ? $team
                    : null,
            ],
            'settings' => fn () => $team
                ? Wrmd::settings()->all()//$team->settingsStore()->all()
                : [],
            'options' => fn () => $team
                ? OptionsStore::all()
                : [],
            'extensions' => fn () => $team
                ? ExtensionNavigation::all()
                : [],
            'recentUpdatedPatients' => fn () => $team
                ? RecentPatients::updated($team)
                : [],
            'recentAdmittedPatients' => fn () => $team
                ? RecentPatients::admitted($team)
                : [],
            'notification' => [
                'heading' => fn () => $request->session()->get('notification.heading'),
                'text' => fn () => $request->session()->get('notification.text'),
                'style' => fn () => $request->session()->get('notification.style'),
            ],
            'unreadNotifications' => []
            // fn () => $team
            //     ? $team->unreadNotifications->transform(function ($notification) {
            //         $notification->created_at_for_humans = $notification->created_at->diffForHumans();
            //         return $notification;
            //     })
            //     : [],
        ];
    }
}
