<?php

namespace App\Http\Controllers\Hotline;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Incident;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentPatientController extends Controller
{
    public function __invoke(Request $request, Incident $incident): RedirectResponse
    {
        $request->validate([
            'case_number' => 'required|confirmed',
        ]);

        $incident->validateOwnership(Auth::user()->current_team_id);

        [$year, $caseId] = explode('-', $request->case_number);
        $year = is_year($year) ? $year : Carbon::createFromFormat('y', $year)->format('Y');

        $admission = Admission::query()
            ->where([
                'case_id' => $caseId,
                'case_year' => $year,
                'team_id' => Auth::user()->current_team_id,
            ])
            ->first();

        if (is_null($admission)) {
            return redirect()->route('hotline.open.index')
                ->with('notification.Style', 'danger')
                ->with('notification.heading', 'Oops!')
                ->with('notification.text', __('A patient with the case number :caseNumber was not found in your account.', [
                    'caseNumber' => $request->case_number,
                ]));
        }

        $incident->patient_id = $admission->patient_id;
        $incident->save();

        return redirect()->route('hotline.incident.edit', $incident)
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Patient :caseNumber was connected to hotline incident :incidentNumber.', [
                'caseNumber' => $request->case_number,
                'incidentNumber' => $incident->incident_number,
            ]));
    }
}
