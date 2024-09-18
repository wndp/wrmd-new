<?php

namespace App\Http\Controllers;

use App\Actions\SaveNewRecheck;
use App\Http\Requests\RecheckRequest;
use App\Models\Admission;
use App\Models\Patient;
use App\Models\Recheck;
use Illuminate\Support\Facades\Auth;

class RecheckController extends Controller
{
    /**
     * Store a new recheck.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RecheckRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        SaveNewRecheck::run($patient->id, [
            'recheck_start_at' => $request->input('recheck_start_at'),
            'recheck_end_at' => $request->input('recheck_end_at'),
            'frequency_id' => $request->frequency_id,
            'assigned_to_id' => $request->assigned_to_id,
            'description' => $request->description,
        ]);

        $caseNumber = Admission::custody(Auth::user()->currentTeam, $patient)->case_number;

        return back()
            ->with('notification.heading', __('Recheck Created'))
            ->with('notification.text', __('Your recheck was assigned to patient :caseNumber.', ['caseNumber' => $caseNumber]));
    }

    /**
     * Update a recheck.
     */
    public function update(RecheckRequest $request, Patient $patient, Recheck $recheck)
    {
        $recheck->validateOwnership(Auth::user()->current_team_id)
            ->update([
                'recheck_start_at' => $request->input('recheck_start_at'),
                'recheck_end_at' => $request->input('recheck_end_at'),
                'frequency_id' => $request->input('frequency_id'),
                'assigned_to_id' => $request->input('assigned_to_id'),
                'description' => $request->input('description'),
            ]);

        $caseNumber = Admission::custody(Auth::user()->currentTeam, $patient)->case_number;

        return back()
            ->with('notification.heading', __('Recheck Updated'))
            ->with('notification.text', __('Your recheck for patient :caseNumber was updated.', ['caseNumber' => $caseNumber]));
    }
}
