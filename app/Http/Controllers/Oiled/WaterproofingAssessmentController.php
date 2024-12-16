<?php

namespace App\Http\Controllers\Oiled;

use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveOilWaterproofingAssessmentRequest;
use App\Models\AttributeOption;
use App\Models\OilWaterproofingAssessment;
use App\Models\Patient;
use App\Repositories\OptionsStore;
use App\Support\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WaterproofingAssessmentController extends Controller
{
    public function index()
    {
        $admission = $this->loadAdmissionAndSharePagination();

        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::OILED_CONDITIONING_BUOYANCIES->value,
                AttributeOptionName::OILED_CONDITIONING_HAULED_OUTS->value,
                AttributeOptionName::OILED_CONDITIONING_PREENINGS->value,
                AttributeOptionName::OILED_CONDITIONING_UNKNOWN_BOOL->value,
                AttributeOptionName::OILED_CONDITIONING_AREAS_WET_TO_SKIN->value,
            ]),
        ]);

        $assessments = $admission
            ->patient
            ->load([
                'oilWaterproofingAssessments' => fn ($q) => $q->orderByDesc('date_evaluated_at')->orderByDesc('time_evaluated_at'),
                'oilWaterproofingAssessments.buoyancy',
                'oilWaterproofingAssessments.hauledOut',
                'oilWaterproofingAssessments.preening',
            ])
            ->oilWaterproofingAssessments
            ->transform(fn ($assessments) => [
                ...$assessments->toArray(),
                'evaluated_at_for_humans' => Timezone::convertFromUtcToLocal($assessments->evaluated_at)->translatedFormat(config('wrmd.date_time_format')),
                'buoyancy' => $assessments->Buoyancy?->value,
                'hauled_out' => $assessments->hauledOut?->value,
                'preening' => $assessments->preening?->value,
                'area_wet_to_skin_for_humans' => implode(', ', $assessments->areas_wet_to_skin ?? []),
            ]);

        return Inertia::render('Oiled/WaterproofingAssessment', [
            'patient' => $admission->patient,
            'assessments' => $assessments,
        ]);
    }

    public function store(SaveOilWaterproofingAssessmentRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        tap(new OilWaterproofingAssessment($this->formatRequestInput($request)), function ($wash) use ($patient) {
            $wash->patient()->associate($patient);
            $wash->save();
        });

        return back();
    }

    public function update(SaveOilWaterproofingAssessmentRequest $request, Patient $patient, OilWaterproofingAssessment $assessment)
    {
        $assessment->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $assessment->update($this->formatRequestInput($request));

        return back();
    }

    public function destroy(Request $request, Patient $patient, OilWaterproofingAssessment $assessment)
    {
        $assessment->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $assessment->delete();

        return back();
    }

    private function formatRequestInput($request)
    {
        $evaluatedAt = Timezone::convertFromLocalToUtc($request->input('evaluated_at'));

        return [
            'date_evaluated_at' => $evaluatedAt->toDateString(),
            'time_evaluated_at' => $evaluatedAt->toTimeString(),
            'buoyancy_id' => $request->input('buoyancy_id'),
            'hauled_out_id' => $request->input('hauled_out_id'),
            'preening_id' => $request->input('preening_id'),
            'self_feeding_id' => $request->input('self_feeding_id'),
            'flighted_id' => $request->input('flighted_id'),
            'areas_wet_to_skin' => $request->input('areas_wet_to_skin'),
            'observations' => $request->input('observations'),
            'examiner' => $request->input('examiner'),
        ];
    }
}
