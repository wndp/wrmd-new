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
                'array'
            ],
            'samples_collected.*' => [
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::NECROPSY_SAMPLES),
            ],
            'other_sample' => [
                'nullable',
                'array'
            ],
            'morphologic_diagnosis' => [
                'nullable',
                'string'
            ],
            'gross_summary_diagnosis' => [
                'nullable',
                'string'
            ],
        ]);

        $necropsy = Necropsy::firstOrNew(['patient_id' => $patient->id]);
        $necropsy->patient_id = $patient->id;
        $necropsy->fill($data);
        $necropsy->save();

        return back();
    }
}
