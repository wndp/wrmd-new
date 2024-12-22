<?php

namespace App\Http\Controllers\People;

use App\Enums\AttributeOptionName;
use App\Events\PersonUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\AttributeOption;
use App\Models\Person;
use App\Options\LocaleOptions;
use App\Repositories\OptionsStore;
use App\Repositories\PeopleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PersonController extends Controller
{
    public function index(Request $request)
    {
        $team = Auth::user()->currentTeam;

        $group = $request->segment(2);
        $people = match ($group) {
            'rescuers' => PeopleRepository::rescuers($team, $request->get('search')),
            'reporting-parties' => PeopleRepository::reportingParties($team, $request->get('search')),
            'volunteers' => PeopleRepository::volunteers($team, $request->get('search')),
            'members' => PeopleRepository::members($team, $request->get('search')),
            'donors' => PeopleRepository::donors($team, $request->get('search')),
            default => [],
        };

        return Inertia::render('People/Index', compact('group', 'people'));
    }

    public function create()
    {
        OptionsStore::add([
            new LocaleOptions,
            AttributeOption::getDropdownOptions([
                AttributeOptionName::PERSON_ENTITY_TYPES->value,
            ]),
        ]);

        return Inertia::render('People/Create');
    }

    public function store(UpdatePersonRequest $request)
    {
        $person = Person::create([
            'team_id' => Auth::user()->currentTeam->id,
            'entity_id' => $request->integer('entity_id'),
            'organization' => $request->input('organization'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone' => $request->input('phone'),
            'alternate_phone' => $request->input('alternate_phone'),
            'email' => $request->input('email'),
            'subdivision' => $request->input('subdivision'),
            'city' => $request->input('city'),
            'address' => $request->input('address'),
            'postal_code' => $request->input('postal_code'),
            'notes' => $request->input('notes'),
            'no_solicitations' => $request->boolean('no_solicitations'),
            'is_volunteer' => $request->boolean('is_volunteer'),
            'is_member' => $request->boolean('is_member'),
        ]);

        event(new PersonUpdated($person));

        return redirect()->route('people.edit', $person)
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __(':name was created.', [
                'name' => $person->identifier,
            ]));
    }

    /**
     * Show the form to edit a person.
     */
    public function edit(Person $person)
    {
        OptionsStore::add([
            new LocaleOptions,
            AttributeOption::getDropdownOptions([
                AttributeOptionName::PERSON_ENTITY_TYPES->value,
            ]),
        ]);

        $person->validateOwnership(Auth::user()->current_team_id)->append(['is_rescuer', 'is_donor', 'is_reporting_party']);

        return Inertia::render('People/Edit', compact('person'));
    }

    /**
     * Update the person in storage.
     */
    public function update(UpdatePersonRequest $request, Person $person)
    {
        $person->validateOwnership(Auth::user()->current_team_id);

        $person->update([
            'entity_id' => $request->integer('entity_id'),
            'organization' => $request->input('organization'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone' => $request->input('phone'),
            'alternate_phone' => $request->input('alternate_phone'),
            'email' => $request->input('email'),
            'subdivision' => $request->input('subdivision'),
            'city' => $request->input('city'),
            'address' => $request->input('address'),
            'postal_code' => $request->input('postal_code'),
            'notes' => $request->input('notes'),
            'no_solicitations' => $request->boolean('no_solicitations'),
            'is_volunteer' => $request->boolean('is_volunteer'),
            'is_member' => $request->boolean('is_member'),
        ]);

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
                ->with('notification.heading', __('Oops!'))
                ->with('notification.text', __('There was a problem deleting this person.'));
        }

        return redirect()->route('people.rescuers.index')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __(':name was deleted from your account.', ['name' => $person->identifier]));
    }
}
