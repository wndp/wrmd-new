<?php

namespace App\Http\Controllers\Necropsy;

use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Models\Necropsy;
use App\Models\Patient;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NecropsyMorphometricsController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $data = $request->validate([
            'sex_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_SEXES),
            ],
            'weight' => [
                'nullable',
                'numeric',
            ],
            'weight_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_WEIGHT_UNITS),
            ],
            'body_condition_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_CONDITIONS),
            ],
            'age' => [
                'nullable',
                'numeric',
            ],
            'age_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_AVES_AGE_UNITS),
            ],
            'wing' => [
                'nullable',
                'numeric',
            ],
            'tarsus' => [
                'nullable',
                'numeric',
            ],
            'culmen' => [
                'nullable',
                'numeric',
            ],
            'exposed_culmen' => [
                'nullable',
                'numeric',
            ],
            'bill_depth' => [
                'nullable',
                'numeric',
            ],
        ]);

        Necropsy::updateOrCreate(['patient_id' => $patient->id], [
            'sex_id' => $request->input('sex_id'),
            'weight' => $request->input('weight'),
            'weight_unit_id' => $request->input('weight_unit_id'),
            'body_condition_id' => $request->input('body_condition_id'),
            'age' => $request->input('age'),
            'age_unit_id' => $request->input('age_unit_id'),
            'wing' => $request->input('wing'),
            'tarsus' => $request->input('tarsus'),
            'culmen' => $request->input('culmen'),
            'exposed_culmen' => $request->input('exposed_culmen'),
            'bill_depth' => $request->input('bill_depth'),
        ]);

        return back();
    }
}
