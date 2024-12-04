<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Events\TeamUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTeamProfileRequest;
use App\Jobs\DeleteTeam;
use App\Models\Team;
use App\Options\LocaleOptions;
use App\Options\Options;
use App\Repositories\AdministrativeDivision;
use App\Repositories\OptionsStore;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        OptionsStore::add([
            new LocaleOptions(),
            'accountStatusOptions' => Options::enumsToSelectable(AccountStatus::cases()),
        ]);

        $teams = Team::query()
            ->when(
                $request->get('status'),
                fn ($query, $status) => $query->where('status', $status),
                fn ($query) => $query->where('status', AccountStatus::ACTIVE->value)
            )
            ->when($request->only([
                'name',
                'federal_permit_number',
                'subdivision_permit_number',
                'contact_name',
                'country',
                'address',
                'city',
                'subdivision',
                'postal_code',
                'coordinates',
                //'phone_number',
                'contact_email',
                'website',
            ]), function ($query, $requestData) {
                foreach ($requestData as $key => $value) {
                    $query->where($key, 'like', "%$value%");
                }
            })
            ->orderBy('name')
            ->paginate()
            ->withQueryString()
            ->through(fn ($team) => [
                ...$team->toArray(),
                'status_for_humans' => $team->status->label(),
                'locale' => $team->formatted_inline_address
            ]);

        if ($teams->total() === 1) {
            return redirect()->route('teams.show', ['team' => $teams->first()['id']]);
        }

        return Inertia::render('Admin/Teams/Index', compact('teams'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(Team $team): Response
    {
        $analyticFiltersForAllYears = [
            'segments' => ['All Patients'],
            'date_period' => 'all-dates',
            'date_from' => '',
            'date_to' => '',
            'compare' => false,
            'group_by_period' => 'month',
            'teamId' => $team->id,
        ];

        $analyticFiltersForThisYear = [
            'segments' => ['All Patients'],
            'date_from' => Carbon::now()->startOfYear()->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
            'compare' => false,
            'teamId' => $team->id,
        ];

        $analyticFiltersForLastYear = [
            'segments' => ['All Patients'],
            'date_from' => Carbon::now()->subYear()->startOfYear()->format('Y-m-d'),
            'date_to' => Carbon::now()->subYear()->format('Y-m-d'),
            'compare' => false,
            'teamId' => $team->id,
        ];

        $analyticFiltersForThisWeek = [
            'segments' => ['All Patients'],
            'date_from' => Carbon::now()->startOfWeek()->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
            'compare' => false,
            'teamId' => $team->id,
        ];

        $team->load('masterAccount');

        $masterAccounts = Options::arrayToSelectable(
            Team::where('is_master_account', true)
                ->get()
                ->pluck('name', 'id')
                ->toArray()
        );

        return Inertia::render('Admin/Teams/Show', compact(
            'team',
            'masterAccounts',
            'analyticFiltersForAllYears',
            'analyticFiltersForThisYear',
            'analyticFiltersForLastYear',
            'analyticFiltersForThisWeek'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team): Response
    {
        $locale = new AdministrativeDivision('en', $team->country);
        $team->load('masterAccount');

        OptionsStore::add([
            new LocaleOptions(),
            'accountStatusOptions' => Options::enumsToSelectable(AccountStatus::cases()),
            'subdivisionOptions' => Options::arrayToSelectable($locale->countrySubdivisions()),
            'timezoneOptions' => Options::arrayToSelectable($locale->countryTimeZones()),
        ]);

        return Inertia::render('Admin/Teams/Edit', compact('team'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamProfileRequest $request, Team $team): RedirectResponse
    {
        $team->update($request->only([
            'status',
            'is_master_account',
            'name',
            'country',
            'address',
            'city',
            'subdivision',
            'postal_code',
            'contact_name',
            'phone_number',
            'contact_email',
            'website',
            'federal_permit_number',
            'subdivision_permit_number',
            'notes',
        ]));

        event(new TeamUpdated($team));

        return redirect()->route('teams.edit', $team);
    }

    /**
     * Show the form to destroy the specified resource from storage.
     */
    public function delete(Team $team): Response
    {
        return Inertia::render('Admin/Teams/Delete', compact(
            'team'
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Team $team): RedirectResponse
    {
        $request->offsetSet('name', Str::slug(request('name')));

        $request->validate([
            'name' => 'required|in:'.Str::slug($team->name),
            'password' => ['required', 'confirmed', 'current_password'],
        ], [
            'name.in' => 'The provided organization name does not match the displayed account organization name.',
        ]);

        DeleteTeam::dispatch($team);

        return redirect()->route('admin.dashboard')
            ->with('notification.heading', 'I Hope You Meant That!')
            ->with('notification.text', "$team->name is in the queue to be deleted.");
    }
}
