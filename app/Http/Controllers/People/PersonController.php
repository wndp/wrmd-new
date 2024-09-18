<?php

namespace App\Http\Controllers\People;

use App\Events\PersonUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PersonController extends Controller
{
    public function index(Request $request)
    {
        $account = Auth::user()->currentAccount;

        $group = $request->segment(2);
        $people = match ($group) {
            'rescuers' => PeopleRepository::rescuers($account, $request->get('search')),
            'reporting-parties' => PeopleRepository::reportingParties($account, $request->get('search')),
            'volunteers' => PeopleRepository::volunteers($account, $request->get('search')),
            'members' => PeopleRepository::members($account, $request->get('search')),
            'donors' => PeopleRepository::donors($account, $request->get('search')),
            default => [],
        };

        return Inertia::render('People/Index', compact('group', 'people'));
    }

    public function create(PeopleOptions $peopleOptions, LocaleOptions $localeOptions)
    {
        OptionsStore::merge($peopleOptions);
        OptionsStore::merge($localeOptions);

        return Inertia::render('People/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization' => 'required_without:first_name',
            'first_name' => 'required_without:organization',
            'email' => 'nullable|email',
            'no_solicitations' => 'nullable|boolean',
            'is_volunteer' => 'nullable|boolean',
            'is_member' => 'nullable|boolean',
        ]);

        $person = new Person($request->only(
            'entity',
            'organization',
            'first_name',
            'last_name',
            'phone',
            'alt_phone',
            'email',
            'subdivision',
            'city',
            'address',
            'postal_code',
            'notes',
            'no_solicitations',
            'is_volunteer',
            'is_member'
        ));

        $person->account_id = Auth::user()->currentAccount->id;
        $person->save();

        event(new PersonUpdated($person));

        return redirect()->route('people.edit', $person)
            ->with('flash.notificationHeading', 'Person Created!')
            ->with('flash.notification', "$person->identifier was created.");
    }

    /**
     * Show the form to edit a person.
     */
    public function edit(Person $person, PeopleOptions $peopleOptions, LocaleOptions $localeOptions)
    {
        OptionsStore::merge($peopleOptions);
        OptionsStore::merge($localeOptions);

        $person->validateOwnership(Auth::user()->current_team_id)->append(['is_rescuer', 'is_donor', 'is_reporting_party']);

        return Inertia::render('People/Edit', compact('person'));
    }

    /**
     * Update the person in storage.
     */
    public function update(UpdatePersonRequest $request, Person $person)
    {
        $person->validateOwnership(Auth::user()->current_team_id);

        $person->update($request->only(
            'entity_id',
            'organization',
            'first_name',
            'last_name',
            'phone',
            'alt_phone',
            'email',
            'subdivision',
            'city',
            'address',
            'postal_code',
            'notes',
            'no_solicitations',
            'is_volunteer',
            'is_member'
        ));

        event(new PersonUpdated($person));

        return back();
    }

    public function destroy(Person $person)
    {
        $person->validateOwnership(Auth::user()->current_team_id);

        try {
            $person->delete();
        } catch (DatabaseException $e) {
            return redirect()->route('people.rescuers.index')
                ->with('flash.notificationHeading', __('Oops!'))
                ->with('flash.notification', __('There was a problem deleting this person.'));
        }

        return redirect()->route('people.rescuers.index')
            ->with('flash.notificationHeading', __('Person Deleted'))
            ->with('flash.notification', __(':name was deleted from your account.', ['name' => $person->identifier]));
    }
}
