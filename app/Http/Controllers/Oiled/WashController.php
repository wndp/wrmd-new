<?php

namespace App\Http\Controllers\Oiled;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveOilWashRequest;
use App\Models\AttributeOption;
use App\Models\OilWash;
use App\Models\Patient;
use App\Repositories\OptionsStore;
use App\Support\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class WashController extends Controller
{
    public function index(): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();

        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::OILED_WASH_PRE_TREATMENTS->value,
                AttributeOptionName::OILED_WASH_TYPES->value,
                AttributeOptionName::OILED_WASH_DETERGENTS->value,
                AttributeOptionName::OILED_WASH_DRYING_METHODS->value,
            ])
        ]);

        [
            $preTreatmentIsNoneId,
            $washTypeIsInitialId,
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::OILED_WASH_PRE_TREATMENTS->value, AttributeOptionUiBehavior::OILED_WASH_PRE_TREATMENT_IS_NONE->value],
            [AttributeOptionName::OILED_WASH_TYPES->value, AttributeOptionUiBehavior::OILED_WASH_TYPE_IS_INITIAL->value]
        ]);

        $washes = $admission
            ->patient
            ->load([
                'oilWashes' => fn ($q) => $q->orderByDesc('date_washed_at')->orderByDesc('time_washed_at'),
                'oilWashes.preTreatment',
                'oilWashes.washType',
                'oilWashes.detergent',
                'oilWashes.dryingMethod'
            ])
            ->oilWashes
            ->transform(fn ($wash) => [
                ...$wash->toArray(),
                'washed_at_for_humans' => Timezone::convertFromUtcToLocal($wash->washed_at)->translatedFormat(config('wrmd.date_time_format')),
                'pre_treatment' => $wash->preTreatment?->value,
                'wash_type' => $wash->washType?->value,
                'detergent' => $wash->detergent?->value,
                'drying_method' => $wash->dryingMethod?->value,
            ]);

        return Inertia::render('Oiled/Wash', [
            'patient' => $admission->patient,
            'washes' => $washes,
            'preTreatmentIsNoneId' => $preTreatmentIsNoneId,
            'washTypeIsInitialId' => $washTypeIsInitialId,
        ]);
    }

    public function store(SaveOilWashRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        tap(new OilWash($this->formatRequestInput($request)), function ($wash) use ($patient) {
            $wash->patient()->associate($patient);
            $wash->save();
        });

        return back();
    }

    /**
     * Update a wash in storage.
     */
    public function update(SaveOilWashRequest $request, Patient $patient, OilWash $wash)
    {
        $wash->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $wash->update($this->formatRequestInput($request));

        return back();
    }

    /**
     * Delete wash from storage.
    */
    public function destroy(Request $request, Patient $patient, OilWash $wash)
    {
        $wash->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $wash->delete();

        return back();
    }

    private function formatRequestInput($request): array
    {
        $washedAt = Timezone::convertFromLocalToUtc($request->input('washed_at'));

        return [
            'date_washed_at' => $washedAt->toDateString(),
            'time_washed_at' => $washedAt->toTimeString(),
            'pre_treatment_id' => $request->input('pre_treatment_id'),
            'pre_treatment_duration' => $request->input('pre_treatment_duration'),
            'wash_type_id' => $request->input('wash_type_id'),
            'wash_duration' => $request->input('wash_duration'),
            'detergent_id' => $request->input('detergent_id'),
            'rinse_duration' => $request->input('rinse_duration'),
            'washer' => $request->input('washer'),
            'handler' => $request->input('handler'),
            'rinser' => $request->input('rinser'),
            'dryer' => $request->input('dryer'),
            'drying_method_id' => $request->input('drying_method_id'),
            'drying_duration' => $request->input('drying_duration'),
            'comments' => $request->input('comments'),
        ];
    }
}
