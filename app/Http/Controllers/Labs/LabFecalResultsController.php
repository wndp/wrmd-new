<?php

namespace App\Http\Controllers\Labs;

use App\Http\Controllers\Controller;
use App\Http\Requests\LabFecalResultRequest;
use App\Models\LabFecalResult;
use App\Models\LabReport;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class LabFecalResultsController extends Controller
{
    public function store(LabFecalResultRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $labFecalResult = LabFecalResult::create([
            'float_id' => $request->input('float_id'),
            'direct_id' => $request->input('direct_id'),
            'centrifugation_id' => $request->input('centrifugation_id'),
        ]);

        $labReport = new LabReport([
            'patient_id' => $patient->id,
            'analysis_date_at' => $request->input('analysis_date_at'),
            'technician' => $request->input('technician'),
            'accession_number' => $request->input('accession_number'),
            'analysis_facility' => $request->input('analysis_facility'),
            'comments' => $request->input('comments'),
        ]);

        $labFecalResult->labReport()->save($labReport);

        return back()
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your fecal analysis was created.'));
    }

    public function update(LabFecalResultRequest $request, Patient $patient, LabFecalResult $labResult)
    {
        $labResult->labReport->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $labResult->update([
            'float_id' => $request->input('float_id'),
            'direct_id' => $request->input('direct_id'),
            'centrifugation_id' => $request->input('centrifugation_id'),
        ]);

        $labResult->labReport->update([
            'analysis_date_at' => $request->input('analysis_date_at'),
            'technician' => $request->input('technician'),
            'accession_number' => $request->input('accession_number'),
            'analysis_facility' => $request->input('analysis_facility'),
            'comments' => $request->input('comments'),
        ]);

        return back()
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your fecal analysis was updated.'));
    }
}
