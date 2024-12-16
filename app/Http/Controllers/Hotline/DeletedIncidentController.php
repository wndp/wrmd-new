<?php

namespace App\Http\Controllers\Hotline;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeletedIncidentController extends Controller
{
    /**
     * Restore a deleted incident.
     */
    public function destroy(Request $request, Incident $incident): RedirectResponse
    {
        $request->validate([
            'incident_number' => 'required|in:'.$incident->incident_number,
            'password' => 'required|current_password',
        ], [
            'incident_number.in' => __('The provided incident number does not match the displayed incident number.'),
        ]);

        $incident->validateOwnership(Auth::user()->current_team_id);

        $incident->restore();

        return redirect()->route('hotline.open.index')
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Hotline incident restored.'));
    }
}
