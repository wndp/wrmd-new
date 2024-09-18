<?php

namespace App\Http\Controllers;

use App\Caches\PatientSelector;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class SelectPatientController extends Controller
{
    /**
     * Add a patient to the cache of selected patients.
     *
     * @return void
     */
    public function store(Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);
        PatientSelector::add($patient->id);

        return back();
    }

    /**
     * Remove a patient from the cache of selected patients.
     *
     * @return void
     */
    public function destroy(Patient $patient = null)
    {
        if (is_null($patient)) {
            PatientSelector::empty();
        } else {
            $patient->validateOwnership(Auth::user()->current_team_id);
            PatientSelector::remove($patient->id);
        }

        return back();
    }
}
