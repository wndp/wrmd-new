<?php

namespace App\Http\Controllers\Necropsy;

use App\Domain\Patients\Patient;
use App\Extensions\Necropsy\Necropsy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NecropsySystemsController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_account_id);

        $necropsy = Necropsy::firstOrNew(['patient_id' => $patient->id]);
        $necropsy->patient_id = $patient->id;
        $necropsy->fill($request->only([
            'integument_finding',
            'integument',
            'cavities_finding',
            'cavities',
            'gastrointestinal_finding',
            'gastrointestinal',
            'liver_gallbladder_finding',
            'liver_gallbladder',
            'hematopoietic_finding',
            'hematopoietic',
            'renal_finding',
            'renal',
            'respiratory_finding',
            'respiratory',
            'cardiovascular_finding',
            'cardiovascular',
            'endocrine_reproductive_finding',
            'endocrine_reproductive',
            'nervous_finding',
            'nervous',
            'head_finding',
            'head',
            'musculoskeletal_finding',
            'musculoskeletal',
        ]));
        $necropsy->save();

        return back();
    }
}
