<?php

namespace App\Http\Controllers\Labs;

use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\LabCytologyResultRequest;
use App\Models\AttributeOption;
use App\Models\LabCytologyResult;
use App\Models\LabReport;
use App\Models\Patient;
use App\Repositories\OptionsStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LabCytologyResultsController extends Controller
{
    public function store(LabCytologyResultRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $labCytologyResult = LabCytologyResult::create([
            'source' => $request->input('source')
        ]);

        $labReport = new LabReport([
            'patient_id' => $patient->id,
            'analysis_date_at' => $request->input('analysis_date_at'),
            'technician' => $request->input('technician'),
            'accession_number' => $request->input('accession_number'),
            'analysis_facility' => $request->input('analysis_facility'),
            'comments' => $request->input('comments'),
        ]);

        $labCytologyResult->labReport()->save($labReport);

        return back()
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your cytology analysis was created.'));
    }

    public function update(LabCytologyResultRequest $request, Patient $patient, LabCytologyResult $labResult)
    {
        $labResult->labReport->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $labResult->update([
            'source' => $request->input('source')
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
            ->with('notification.text', __('Your cytology analysis was updated.'));
    }
}
