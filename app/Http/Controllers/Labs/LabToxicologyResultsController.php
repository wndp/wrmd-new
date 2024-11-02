<?php

namespace App\Http\Controllers\Labs;

use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\LabToxicologyResultRequest;
use App\Models\AttributeOption;
use App\Models\LabToxicologyResult;
use App\Models\LabReport;
use App\Models\Patient;
use App\Repositories\OptionsStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LabToxicologyResultsController extends Controller
{
    public function store(LabToxicologyResultRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $LabToxicologyResult = LabToxicologyResult::create($request->only([
            'toxin_id',
            'level',
            'level_unit_id',
            'source',
        ]));

        $labReport = new LabReport([
            'patient_id' => $patient->id,
            'analysis_date_at' => $request->input('analysis_date_at'),
            'technician' => $request->input('technician'),
            'accession_number' => $request->input('accession_number'),
            'analysis_facility' => $request->input('analysis_facility'),
            'comments' => $request->input('comments'),
        ]);

        $LabToxicologyResult->labReport()->save($labReport);

        return back()
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your toxicology analysis was created.'));
    }

    public function update(LabToxicologyResultRequest $request, Patient $patient, LabToxicologyResult $labResult)
    {
        $labResult->labReport->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $labResult->update($request->only([
            'toxin_id',
            'level',
            'level_unit_id',
            'source',
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
            ->with('notification.text', __('Your toxicology analysis was updated.'));
    }
}
