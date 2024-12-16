<?php

namespace App\Http\Controllers\Labs;

use App\Http\Controllers\Controller;
use App\Http\Requests\LabChemistryResultRequest;
use App\Models\LabChemistryResult;
use App\Models\LabReport;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class LabChemistryResultsController extends Controller
{
    public function store(LabChemistryResultRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $LabChemistryResult = LabChemistryResult::create($request->only([
            'ast',
            'ck',
            'ggt',
            'amy',
            'alb',
            'alp',
            'alt',
            'tp',
            'glob',
            'bun',
            'chol',
            'crea',
            'ba',
            'ca',
            'ca_unit_id',
            'p',
            'p_unit_id',
            'cl',
            'cl_unit_id',
            'k',
            'k_unit_id',
            'na',
            'na_unit_id',
            'total_bilirubin',
            'ag_ratio',
            'tri',
            'nak_ratio',
            'cap_ratio',
            'glu',
            'ua',
        ]));

        $labReport = new LabReport([
            'patient_id' => $patient->id,
            'analysis_date_at' => $request->input('analysis_date_at'),
            'technician' => $request->input('technician'),
            'accession_number' => $request->input('accession_number'),
            'analysis_facility' => $request->input('analysis_facility'),
            'comments' => $request->input('comments'),
        ]);

        $LabChemistryResult->labReport()->save($labReport);

        return back()
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your CBC (Complete Blood Count) was created.'));
    }

    public function update(LabChemistryResultRequest $request, Patient $patient, LabChemistryResult $labResult)
    {
        $labResult->labReport->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $labResult->update($request->only([
            'ast',
            'ck',
            'ggt',
            'amy',
            'alb',
            'alp',
            'alt',
            'tp',
            'glob',
            'bun',
            'chol',
            'crea',
            'ba',
            'ca',
            'ca_unit_id',
            'p',
            'p_unit_id',
            'cl',
            'cl_unit_id',
            'k',
            'k_unit_id',
            'na',
            'na_unit_id',
            'total_bilirubin',
            'ag_ratio',
            'tri',
            'nak_ratio',
            'cap_ratio',
            'glu',
            'ua',
        ]));

        $labResult->labReport->update([
            'analysis_date_at' => $request->input('analysis_date_at'),
            'technician' => $request->input('technician'),
            'accession_number' => $request->input('accession_number'),
            'analysis_facility' => $request->input('analysis_facility'),
            'comments' => $request->input('comments'),
        ]);

        return back()
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your CBC (Complete Blood Count) was updated.'));
    }
}
