<?php

namespace App\Http\Controllers\Labs;

use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\LabCbcResultRequest;
use App\Models\AttributeOption;
use App\Models\LabCbcResult;
use App\Models\LabReport;
use App\Models\Patient;
use App\Repositories\OptionsStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LabCbcResultsController extends Controller
{
    public function store(LabCbcResultRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $labCbcResult = LabCbcResult::create($request->only([
            'packed_cell_volume',
            'total_solids',
            'buffy_coat',
            'plasma_color',
            'white_blod_cell_count',
            'white_blod_cell_count_unit_id',
            'segmented_neutrophils',
            'segmented_neutrophils_unit_id',
            'band_neutrophils',
            'band_neutrophils_unit_id',
            'eosinophils',
            'eosinophils_unit_id',
            'basophils',
            'basophils_unit_id',
            'lymphocytes',
            'lymphocytes_unit_id',
            'monocytes',
            'monocytes_unit_id',
            'hemoglobin',
            'mean_corpuscular_volume',
            'mean_corpuscular_hemoglobin',
            'mean_corpuscular_hemoglobin_concentration',
            'erythrocytes',
            'reticulocytes',
            'thrombocytes',
            'polychromasia',
            'anisocytosis',
        ]));

        $labReport = new LabReport([
            'patient_id' => $patient->id,
            'analysis_date_at' => $request->input('analysis_date_at'),
            'technician' => $request->input('technician'),
            'accession_number' => $request->input('accession_number'),
            'analysis_facility' => $request->input('analysis_facility'),
            'comments' => $request->input('comments'),
        ]);

        $labCbcResult->labReport()->save($labReport);

        return back()
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your CBC (Complete Blood Count) was created.'));
    }

    public function update(LabCbcResultRequest $request, Patient $patient, LabCbcResult $labResult)
    {
        $labResult->labReport->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $labResult->update($request->only([
            'packed_cell_volume',
            'total_solids',
            'buffy_coat',
            'plasma_color',
            'white_blod_cell_count',
            'white_blod_cell_count_unit_id',
            'segmented_neutrophils',
            'segmented_neutrophils_unit_id',
            'band_neutrophils',
            'band_neutrophils_unit_id',
            'eosinophils',
            'eosinophils_unit_id',
            'basophils',
            'basophils_unit_id',
            'lymphocytes',
            'lymphocytes_unit_id',
            'monocytes',
            'monocytes_unit_id',
            'hemoglobin',
            'mean_corpuscular_volume',
            'mean_corpuscular_hemoglobin',
            'mean_corpuscular_hemoglobin_concentration',
            'erythrocytes',
            'reticulocytes',
            'thrombocytes',
            'polychromasia',
            'anisocytosis',
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
