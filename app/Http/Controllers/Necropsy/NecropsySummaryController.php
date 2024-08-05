<?php

namespace App\Http\Controllers\Necropsy;

use App\Domain\Patients\Patient;
use App\Extensions\Necropsy\Necropsy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NecropsySummaryController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_account_id);

        $necropsy = Necropsy::firstOrNew(['patient_id' => $patient->id]);
        $necropsy->patient_id = $patient->id;
        $necropsy->fill($request->only([
            'samples_collected',
            'other_sample',
            'morphologic_diagnosis',
            'gross_summary_diagnosis',
        ]));
        $necropsy->save();

        return back();
    }
}
