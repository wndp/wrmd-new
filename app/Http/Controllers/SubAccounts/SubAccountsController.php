<?php

namespace App\Http\Controllers\SubAccounts;

use App\Actions\RegisterSubAccount;
use App\Events\AccountUpdated;
use App\Exceptions\RecordNotOwned;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubAccountRequest;
use App\Http\Requests\UpdateSubAccountRequest;
use App\Models\Team;
use App\Options\LocaleOptions;
use App\Repositories\OptionsStore;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SubAccountsController extends Controller
{
    public function index(): Response
    {
        $subAccounts = Auth::user()
            ->currentTeam
            ->subAccounts()
            ->orderBy('name')
            ->get()
            ->transform(fn ($team) => [
                ...$team->toArray(),
                'locale' => $team->formatted_inline_address,
                'created_at_for_humans' => $team->created_at->translatedFormat(config('wrmd.date_format'))
            ]);

        return Inertia::render('SubAccounts/Index', compact('subAccounts'));
    }

    /**
     * Show the form for creating a new sub-account.
     */
    public function create(): Response
    {
        OptionsStore::add([
            new LocaleOptions()
        ]);

        $users = Auth::user()
            ->currentTeam
            ->users()
            ->where('users.id', '!=', Auth::id())
            ->where('is_api_user', false)
            ->get();

        return Inertia::render('SubAccounts/Create', compact('users'));
    }

    /**
     * Store a newly created sub-account in storage.
     */
    public function store(StoreSubAccountRequest $request): RedirectResponse
    {
        $subAccount = RegisterSubAccount::run(Auth::user(), $request);

        return redirect()->route('sub_accounts.show', $subAccount->id);
    }

    /**
     * Show the form for editing a sub-account.
     */
    public function show(Team $subAccount): Response
    {
        abort_unless(
            Auth::user()->currentTeam->hasSubAccount($subAccount),
            new RecordNotOwned()
        );

        $analyticFiltersForAllYears = [
            'segments' => ['All Patients'],
            'date_period' => 'all-dates',
            'date_from' => '',
            'date_to' => '',
            'compare' => false,
            'group_by_period' => 'month',
            'accountId' => $subAccount->id,
        ];

        $analyticFiltersForThisYear = [
            'segments' => ['All Patients'],
            'date_from' => Carbon::now()->startOfYear()->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
            'compare' => false,
            'accountId' => $subAccount->id,
        ];

        $analyticFiltersForLastYear = [
            'segments' => ['All Patients'],
            'date_from' => Carbon::now()->subYear()->startOfYear()->format('Y-m-d'),
            'date_to' => Carbon::now()->subYear()->format('Y-m-d'),
            'compare' => false,
            'accountId' => $subAccount->id,
        ];

        $analyticFiltersForThisWeek = [
            'segments' => ['All Patients'],
            'date_from' => Carbon::now()->startOfWeek()->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
            'compare' => false,
            'accountId' => $subAccount->id,
        ];

        $subAccount->locale = $subAccount->formatted_inline_address;
        $subAccount->created_at_for_humans = $subAccount->created_at->translatedFormat(config('wrmd.date_format'));

        return Inertia::render('SubAccounts/Show', compact(
            'subAccount',
            'analyticFiltersForAllYears',
            'analyticFiltersForThisYear',
            'analyticFiltersForLastYear',
            'analyticFiltersForThisWeek'
        ));
    }

    /**
     * Show the form for editing a sub-account.
     */
    public function edit(Team $subAccount): Response
    {
        abort_unless(
            Auth::user()->currentTeam->hasSubAccount($subAccount),
            new RecordNotOwned()
        );

        OptionsStore::add([
            new LocaleOptions()
        ]);

        return Inertia::render('SubAccounts/Edit', compact('subAccount'));
    }

    /**
     * Update a sub-account in storage.
     */
    public function update(UpdateSubAccountRequest $request, Team $subAccount): RedirectResponse
    {
        abort_unless(
            Auth::user()->currentTeam->hasSubAccount($subAccount),
            new RecordNotOwned()
        );

        $subAccount->update($request->only([
            'name',
            'country',
            'address',
            'city',
            'subdivision',
            'postal_code',
            'contact_name',
            'phone_number',
            'contact_email',
            'notes',
            'federal_permit_number',
            'subdivision_permit_number',
        ]));

        //event(new AccountUpdated($subAccount));

        return redirect()->route('sub_accounts.show', $subAccount->id);
    }
}
