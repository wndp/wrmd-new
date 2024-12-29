<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCareLogRequest;
use App\Models\CareLog;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class CareLogController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SaveCareLogRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        //CareLog::store($patient->id, collect($request->dataFromRequest()), Auth::user());

        CareLog::create(array_merge(
            $request->dataFromRequest(),
            [
                'patient_id' => $patient->id,
                'user_id' => Auth::id(),
            ]
        ));

        $caseNumber = $patient->admissions->firstWhere('team_id', Auth::user()->current_team_id)->case_number;

        return back()
            ->with('flash.notificationHeading', __('Comment Added'))
            ->with('flash.notification', __('Your comment was add to :caseNumber.', [
                'caseNumber' => $caseNumber,
            ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SaveCareLogRequest $request, Patient $patient, CareLog $care_log)
    {
        $care_log->validateOwnership(Auth::user()->current_team_id)
            ->update($request->dataFromRequest());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient, CareLog $care_log)
    {
        $care_log->validateOwnership(Auth::user()->current_team_id)->delete();

        return back();
    }
}
