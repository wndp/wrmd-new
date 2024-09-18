<?php

namespace App\Http\Controllers;

use App\Models\CareLog;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CareLogController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'date_care_at' => 'required|date',
            'weight' => 'nullable|numeric',
            'weight_unit_id' => 'nullable|required_with:weight|integer',
            'temperature' => 'nullable|numeric',
            'temperature_unit_id' => 'nullable|required_with:temperature|integer',
            'comments' => 'nullable',
        ], [
            'date_care_at.required' => __('The care date field is required.'),
            'date_care_at.date' => __('The care date is not a valid date.'),
        ]);

        CareLog::store($patient->id, collect($request->all()), Auth::user());

        $caseNumber = $patient->admissions->firstWhere('team_id', Auth::user()->current_team_id)->case_number;

        return back()
            ->with('flash.notificationHeading', __('Comment Added'))
            ->with('flash.notification', __('Your comment was add to :caseNumber.', [
                'caseNumber' => $caseNumber
            ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Patient $patient, CareLog $care_log)
    {
        $care_log->validateOwnership(Auth::user()->current_team_id)
            ->update($request->validate([
                'treated_at' => 'required|date',
                'weight' => 'nullable|required_without:comments|numeric',
                'weight_unit_id' => 'nullable',
                'comments' => 'required_without:weight',
            ], [
                'treated_at.required' => 'The treated at date field is required.',
                'treated_at.date' => 'The treated at date is not a valid date.',
            ]));

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
