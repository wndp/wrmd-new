<?php

namespace App\Http\Controllers\Necropsy;

use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Models\Necropsy;
use App\Models\Patient;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NecropsySummaryController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $data = $request->validate([
            'samples_collected' => [
                'nullable',
                'array',
            ],
            'samples_collected.*' => [
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::NECROPSY_SAMPLES),
            ],
            'other_sample' => [
                'nullable',
                'string',
            ],
            'morphologic_diagnosis' => [
                'nullable',
                'string',
            ],
            'gross_summary_diagnosis' => [
                'nullable',
                'string',
            ],
        ]);

        Necropsy::updateOrCreate(['patient_id' => $patient->id], [
            'samples_collected' => $request->input('samples_collected'),
            'other_sample' => $request->input('other_sample'),
            'morphologic_diagnosis' => $request->input('morphologic_diagnosis'),
            'gross_summary_diagnosis' => $request->input('gross_summary_diagnosis'),
        ]);

        return back();
    }
}
