<?php

namespace App\Http\Controllers\Hotline;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Http\Controllers\Controller;
use App\Models\Communication;
use App\Models\Incident;
use App\Support\Timezone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class IncidentCommunicationsController extends Controller
{
    /**
     * Return a listing of a hotline incident's communications.
     */
    public function index(Request $request, Incident $incident): Response
    {
        $incident->validateOwnership(Auth::user()->current_team_id);

        $incident->load('communications', 'status');

        [
            $statusOpenId,
            $statusUnresolvedId,
            $statusResolvedId
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN->value],
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_UNRESOLVED->value],
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED->value]
        ]);

        return Inertia::render('Hotline/Communications', [
            'incident' => $incident,
            'communications' => $incident->communications->transform(fn ($communication) => [
                ...$communication->toArray(),
                'communication_at_for_humans' => Timezone::convertFromUtcToLocal($communication->communication_at)?->toDayDateTimeString(),
                'communication_at_local' => Timezone::convertFromUtcToLocal($communication->communication_at)?->toDateTimeLocalString(),
                'communication_for_humans' => implode(' - ', array_filter([
                    $communication->communication_at_for_humans, $communication->communication_by, $communication->communication
                ]))
            ]),
            'statusOpenId' => $statusOpenId,
            'statusUnresolvedId' => $statusUnresolvedId,
            'statusResolvedId' => $statusResolvedId
        ]);
    }

    /**
     * Store a newly created communication in storage.
     */
    public function store(Request $request, Incident $incident): RedirectResponse
    {
        $incident->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'communication' => 'required',
            'communication_at' => 'required|date',
        ], [
            'communication_at.required' => 'The date responded field is required.',
            'communication_at.date' => 'The date responded field is not a valid date.',
        ]);

        Communication::create([
            'incident_id' => $incident->id,
            'communication' => $request->communication,
            'communication_at' => Timezone::convertFromLocalToUtc($request->input('communication_at')),
            'communication_by' => $request->communication_by,
        ]);

        return redirect()->route('hotline.incident.communications.index', $incident);
    }

    /**
     * Update a communication in storage.
     */
    public function update(Request $request, Incident $incident, Communication $communication): RedirectResponse
    {
        $incident->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'communication' => 'required',
            'communication_at' => 'required|date',
        ], [
            'communication_at.required' => 'The date responded field is required.',
            'communication_at.date' => 'The date responded field is not a valid date.',
        ]);

        $communication->update([
            'communication' => $request->communication,
            'communication_at' => Timezone::convertFromLocalToUtc($request->input('communication_at')),
            'communication_by' => $request->communication_by,
        ]);

        return redirect()->route('hotline.incident.communications.index', $incident);
    }

    /**
     * Delete a communication in storage.
     */
    public function destroy(Request $request, Incident $incident, Communication $communication): RedirectResponse
    {
        $incident->validateOwnership(Auth::user()->current_team_id);

        $communication->delete();

        return redirect()->route('hotline.incident.communications.index', $incident);
    }
}
