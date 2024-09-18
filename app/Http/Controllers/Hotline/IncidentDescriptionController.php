<?php

namespace App\Http\Controllers\Hotline;

use App\Http\Controllers\Controller;
use App\Jobs\GeocodeAddress;
use App\Models\Incident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class IncidentDescriptionController extends Controller
{
    /**
     * Update an incident in storage.
     */
    public function __invoke(Request $request, Incident $incident): RedirectResponse
    {
        $incident->validateOwnership(Auth::user()->current_team_id);

        $incident->update($request->validate([
            'incident_address' => 'nullable',
            'incident_city' => 'nullable',
            'incident_subdivision' => 'nullable',
            'incident_postal_code' => 'nullable',
            'description' => 'nullable',
        ]));

        if ($incident->wasChanged([
            'incident_address',
            'incident_city',
            'incident_subdivision',
            'incident_postal_code',
        ])) {
            GeocodeAddress::dispatch($incident, 'incident_coordinates');
        }

        return redirect()->route('hotline.incident.edit', $incident)
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Hotline incident updated.'));
    }
}
