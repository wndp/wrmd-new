<?php

namespace App\Http\Controllers\Labs;

use App\Http\Controllers\Controller;
use App\Http\Requests\LabUrinalysisResultRequest;
use App\Models\LabReport;
use App\Models\LabUrinalysisResult;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class LabUrinalysisResultsController extends Controller
{
    public function store(LabUrinalysisResultRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $LabUrinalysisResult = LabUrinalysisResult::create($request->only([
            'collection_method_id',
            'sg',
            'ph',
            'pro',
            'glu',
            'ket',
            'bili',
            'ubg',
            'nitrite',
            'bun',
            'leuc',
            'blood',
            'color',
            'turbidity_id',
            'odor_id',
            'crystals',
            'casts',
            'cells',
            'microorganisms',
        ]));

        $labReport = new LabReport([
            'patient_id' => $patient->id,
            'analysis_date_at' => $request->input('analysis_date_at'),
            'technician' => $request->input('technician'),
            'accession_number' => $request->input('accession_number'),
            'analysis_facility' => $request->input('analysis_facility'),
            'comments' => $request->input('comments'),
        ]);

        $LabUrinalysisResult->labReport()->save($labReport);

        return back()
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your urinalysis was created.'));
    }

    public function update(LabUrinalysisResultRequest $request, Patient $patient, LabUrinalysisResult $labResult)
    {
        $labResult->labReport->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $labResult->update($request->only([
            'collection_method_id',
            'sg',
            'ph',
            'pro',
            'glu',
            'ket',
            'bili',
            'ubg',
            'nitrite',
            'bun',
            'leuc',
            'blood',
            'color',
            'turbidity_id',
            'odor_id',
            'crystals',
            'casts',
            'cells',
            'microorganisms',
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
            ->with('notification.text', __('Your urinalysis was updated.'));
    }
}
