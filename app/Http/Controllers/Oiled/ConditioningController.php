<?php

namespace App\Http\Controllers\Oiled;

use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveOilConditioningRequest;
use App\Models\AttributeOption;
use App\Models\OilConditioning;
use App\Models\Patient;
use App\Repositories\OptionsStore;
use App\Support\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ConditioningController extends Controller
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
            ])
        ]);

        $conditionings = $admission
            ->patient
            ->load([
                'oilConditionings' => fn ($q) => $q->orderByDesc('date_evaluated_at')->orderByDesc('time_evaluated_at'),
                'oilConditionings.buoyancy',
                'oilConditionings.hauledOut',
                'oilConditionings.preening',
                'oilConditionings.selfFeeding',
                'oilConditionings.flighted',
            ])
            ->oilConditionings
            ->transform(fn ($conditioning) => [
                ...$conditioning->toArray(),
                'evaluated_at_for_humans' => Timezone::convertFromUtcToLocal($conditioning->evaluated_at)->translatedFormat(config('wrmd.date_time_format')),
                'buoyancy' => $conditioning->Buoyancy?->value,
                'hauled_out' => $conditioning->hauledOut?->value,
                'preening' => $conditioning->preening?->value,
                'self_feeding' => $conditioning->selfFeeding?->value,
                'flighted' => $conditioning->flighted?->value,
                'area_wet_to_skin_for_humans' => implode(', ', $conditioning->areas_wet_to_skin ?? []),
            ]);

        return Inertia::render('Oiled/Conditioning', [
            'patient' => $admission->patient,
            'conditionings' => $conditionings,
        ]);
    }

    public function store(SaveOilConditioningRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        tap(new OilConditioning($this->formatRequestInput($request)), function ($wash) use ($patient) {
            $wash->patient()->associate($patient);
            $wash->save();
        });

        return back();
    }

    /**
     * Update a conditioning in storage.
     */
    public function update(SaveOilConditioningRequest $request, Patient $patient, OilConditioning $conditioning)
    {
        $conditioning->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $conditioning->update($this->formatRequestInput($request));

        return back();
    }

    /**
     * Delete conditioning from storage.
    */
    public function destroy(Request $request, Patient $patient, OilConditioning $conditioning)
    {
        $conditioning->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $conditioning->delete();

        return back();
    }

    /**
     * Format the request input for safe database storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
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
