<?php

namespace App\Http\Controllers\Hotline;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Jobs\GeocodeAddress;
use App\Models\AttributeOption;
use App\Models\Communication;
use App\Models\Incident;
use App\Models\Person;
use App\Options\LocaleOptions;
use App\Repositories\IncidentRepository;
use App\Repositories\OptionsStore;
use App\Support\Timezone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class IncidentController extends Controller
{
    /**
     * Display the hotline index page.
     */
    public function index(Request $request): Response
    {
        $team = Auth::user()->currentTeam;
        $group = $request->segment(2);

        $incidents = match ($group) {
            'open' => IncidentRepository::openIncidents($team, $request->get('search')),
            'resolved' => IncidentRepository::resolvedIncidents($team, $request->get('search')),
            'unresolved' => IncidentRepository::unresolvedIncidents($team, $request->get('search')),
            'deleted' => IncidentRepository::deletedIncidents($team, $request->get('search')),
            default => [],
        };

        return Inertia::render('Hotline/Index', [
            'incidents' => $incidents->through(fn ($incident) => [
                'id' => $incident->id,
                'incident_number' => $incident->incident_number,
                'reported_at_for_humans' => $incident->reported_at?->toDateTimeString(),
                'suspected_species' => $incident->suspected_species,
                'is_priority' => $incident->is_priority ? __('Yes') : __('No'),
                'reporting_party_identifier' => $incident->reporting_party?->identifier,
                'status' => $incident->status?->value,
            ]),
            'group' => $group,
        ]);
    }

    /**
     * Display the page to create a new hotline incident.
     *
     * @param  \App\Domain\Hotline\HotlineOptions  $options
     */
    public function create(): Response
    {
        OptionsStore::add([
            new LocaleOptions,
            AttributeOption::getDropdownOptions([
                AttributeOptionName::PERSON_ENTITY_TYPES->value,
                AttributeOptionName::HOTLINE_WILDLIFE_CATEGORIES->value,
                AttributeOptionName::HOTLINE_ADMINISTRATIVE_CATEGORIES->value,
                AttributeOptionName::HOTLINE_OTHER_CATEGORIES->value,
                AttributeOptionName::HOTLINE_STATUSES->value,
            ]),
        ]);

        [$statusOpenId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::HOTLINE_STATUSES->value,
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN->value,
        ]);

        return Inertia::render('Hotline/Create', [
            'statusOpenId' => $statusOpenId,
        ]);
    }

    /**
     * Store a new hotline incident in storage.
     */
    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        [$statusResolvedId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::HOTLINE_STATUSES->value,
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED->value,
        ]);

        $incident = new Incident([
            'reported_at' => Timezone::convertFromLocalToUtc($request->input('reported_at')),
            'occurred_at' => Timezone::convertFromLocalToUtc($request->input('occurred_at')),
            'incident_status_id' => $request->filled('resolved_at') ? $statusResolvedId : $request->input('incident_status_id'),
            'number_of_animals' => $request->filled('suspected_species') ? $request->input('number_of_animals', 1) : null,
            'recorded_by' => $request->recorded_by,
            'duration_of_call' => $request->duration_of_call,
            'suspected_species' => $request->suspected_species,
            'category_id' => $request->category_id,
            'is_priority' => $request->is_priority,
            'incident_address' => $request->incident_address,
            'incident_city' => $request->incident_city,
            'incident_subdivision' => $request->incident_subdivision,
            'incident_postal_code' => $request->incident_postal_code,
            'description' => $request->description,
            'resolved_at' => Timezone::convertFromLocalToUtc($request->input('resolved_at')),
            'given_information' => $request->given_information,
            'resolution' => $request->resolution,
            'given_information' => $request->boolean('given_information'),
        ]);

        $reportingParty = Person::updateOrCreate(array_filter([
            'id' => $request->input('person_id'),
            'team_id' => Auth::user()->current_team_id,
        ]), [
            ...$request->only([
                'entity_id',
                'organization',
                'first_name',
                'last_name',
                'phone',
                'alternate_phone',
                'email',
                'subdivision',
                'city',
                'address',
                'county',
                'postal_code',
                'notes',
                'no_solicitations',
                'is_volunteer',
                'is_member',
            ]),
            'no_solicitations' => true,
        ]);

        $incident->team_id = Auth::user()->current_team_id;
        $incident->responder_id = $reportingParty->id;
        $incident->save();

        if ($request->filled('communication_at', 'communication')) {
            Communication::create([
                'incident_id' => $incident->id,
                'communication' => $request->input('communication'),
                'communication_at' => Timezone::convertFromLocalToUtc($request->input('communication_at')),
                'communication_by' => $request->input('communication_by'),
            ]);
        }

        if ($incident->incident_address && $incident->incident_city && $incident->incident_subdivision) {
            GeocodeAddress::dispatch($incident, 'incident_coordinates');
        }

        return redirect()->route('hotline.open.index')
            ->with('notification.heading', 'Success!')
            ->with('notification.text', 'Hotline incident saved.');
    }

    /**
     * Display the page to edit an incident.
     *
     * @param  \App\Domain\Hotline\HotlineOptions  $options
     */
    public function edit(Incident $incident): Response
    {
        $incident->validateOwnership(Auth::user()->current_team_id)
            ->load([
                'status',
                'patient.admissions',
            ]);

        OptionsStore::add([
            new LocaleOptions,
            AttributeOption::getDropdownOptions([
                AttributeOptionName::PERSON_ENTITY_TYPES->value,
                AttributeOptionName::HOTLINE_WILDLIFE_CATEGORIES->value,
                AttributeOptionName::HOTLINE_ADMINISTRATIVE_CATEGORIES->value,
                AttributeOptionName::HOTLINE_OTHER_CATEGORIES->value,
                AttributeOptionName::HOTLINE_STATUSES->value,
            ]),
        ]);

        [
            $statusOpenId,
            $statusUnresolvedId,
            $statusResolvedId
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN->value],
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_UNRESOLVED->value],
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED->value],
        ]);

        return Inertia::render('Hotline/Edit', [
            'incident' => [
                ...$incident->toArray(),
                'reported_at_local' => Timezone::convertFromUtcToLocal($incident->reported_at)?->toDateTimeLocalString(),
                'occurred_at_local' => Timezone::convertFromUtcToLocal($incident->occurred_at)?->toDateTimeLocalString(),
                'resolved_at_local' => Timezone::convertFromUtcToLocal($incident->resolved_at)?->toDateTimeLocalString(),
            ],
            'statusOpenId' => $statusOpenId,
            'statusUnresolvedId' => $statusUnresolvedId,
            'statusResolvedId' => $statusResolvedId,
        ]);
    }

    /**
     * Update an incident in storage.
     */
    public function update(UpdateIncidentRequest $request, Incident $incident): RedirectResponse
    {
        $incident->update([
            'reported_at' => Timezone::convertFromLocalToUtc($request->input('reported_at')),
            'occurred_at' => Timezone::convertFromLocalToUtc($request->input('occurred_at')),
            'recorded_by' => $request->input('recorded_by'),
            'duration_of_call' => $request->input('duration_of_call'),
            'suspected_species' => $request->input('suspected_species'),
            'number_of_animals' => $request->filled('suspected_species') ? $request->input('number_of_animals') ?? 1 : null,
            'category_id' => $request->input('category_id'),
            'is_priority' => $request->boolean('is_priority'),
            'incident_status_id' => $request->input('incident_status_id'),
        ]);

        return redirect()->route('hotline.incident.edit', $incident)
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Hotline incident updated.'));
    }

    /**
     * Delete an incident from storage.
     */
    public function destroy(Request $request, Incident $incident): RedirectResponse
    {
        $request->validate([
            'incident_number' => 'required|in:'.$incident->incident_number,
            'password' => ['required', 'current_password'],
        ], [
            'incident_number.in' => __('The provided incident number does not match the displayed incident number.'),
        ]);

        $incident->validateOwnership(Auth::user()->current_team_id)->delete();

        return redirect()->route('hotline.open.index')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Hotline incident deleted.'));
    }
}
