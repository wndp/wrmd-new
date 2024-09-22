<?php

namespace App\Http\Controllers;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Http\Requests\PrescriptionRequest;
use App\Models\Admission;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    /**
     * Store a new prescription.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(PrescriptionRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $prescription = new Prescription([
            'drug' => $request->input('drug'),
            'rx_started_at' => $request->input('rx_started_at'),
            'rx_ended_at' => $request->input('rx_ended_at'),
            'frequency_id' => $request->input('frequency_id'),
            'is_controlled_substance' => $request->input('is_controlled_substance'),
            'concentration' => $request->input('concentration'),
            'concentration_unit_id' => $request->input('concentration_unit_id'),
            'dosage' => $request->input('dosage'),
            'dosage_unit_id' => $request->input('dosage_unit_id'),
            'loading_dose' => $request->input('loading_dose'),
            'loading_dose_unit_id' => $request->input('loading_dose_unit_id'),
            'dose' => $request->input('dose'),
            'dose_unit_id' => $request->input('dose_unit_id'),
            'route_id' => $request->input('route_id'),
            'instructions' => $request->input('instructions'),
        ]);

        [$singleDoseFrequencyId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::DAILY_TASK_FREQUENCIES->value,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SINGLE_DOSE->value,
        ]);

        if ($request->integer('frequency_id') === $singleDoseFrequencyId) {
            $prescription->rx_ended_at = $prescription->rx_started_at;
        }

        $prescription->veterinarian_id = $request->veterinarian_id;
        $prescription->patient()->associate($patient);
        $prescription->save();

        $caseNumber = Admission::custody(Auth::user()->currentTeam, $patient)->case_number;

        return back()
            ->with('notification.heading', __('Prescription Created'))
            ->with('notification.text', __('Your prescription was assigned to patient :caseNumber', ['caseNumber' => $caseNumber]));
    }

    /**
     * Update a prescription.
     */
    public function update(Request $request, Patient $patient, Prescription $prescription)
    {
        $request->validate(
            $this->validationRules(),
            $this->validationRuleMessages()
        );

        $prescription->validateOwnership(Auth::user()->current_team_id)
            ->fill([
                'drug' => $request->drug,
                'rx_started_at' => $request->convertDateFromLocal('rx_started_at'),
                'rx_ended_at' => $request->convertDateFromLocal('rx_ended_at'),
                'frequency' => $request->frequency,
                'is_controlled_substance' => $request->is_controlled_substance,
                'concentration' => $request->concentration,
                'concentration_unit' => $request->concentration_unit,
                'dosage' => $request->dosage,
                'dosage_unit' => $request->dosage_unit,
                'loading_dose' => $request->loading_dose,
                'loading_dose_unit' => $request->loading_dose_unit,
                'dose' => $request->dose,
                'dose_unit' => $request->dose_unit,
                'route' => $request->route,
                'instructions' => $request->instructions,
            ]);

        $prescription->veterinarian_id = $request->veterinarian_id;
        $prescription->save();

        $caseNumber = Admission::custody(Auth::user()->currentTeam, $patient)->case_number;

        return back()
            ->with('notification.heading', __('Prescription Updated'))
            ->with('notification.text', __('Your prescription for patient :caseNumber was updated.', ['caseNumber' => $caseNumber]));
    }
}
