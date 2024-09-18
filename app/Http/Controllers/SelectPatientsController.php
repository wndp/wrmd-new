<?php

namespace App\Http\Controllers;

use App\Caches\PatientSelector;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class SelectPatientsController extends Controller
{
    /**
     * Add a patient to the cache of selected patients.
     *
     * @param  \App\Domain\Patients\Patient  $patient
     * @return void
     */
    public function store()
    {
        $data = request()->validate([
            'patients' => 'required|array',
        ]);

        collect($data['patients'])
            ->filter()
            ->each(function ($patientId) {
                Patient::find($patientId)->validateOwnership(Auth::user()->current_team_id);
                PatientSelector::add($patientId);
            });

        return back();
    }

    /**
     * Remove a patient from the cache of selected patients.
     *
     * @param  \App\Domain\Patients\Patient  $patient
     * @return void
     */
    public function destroy()
    {
        $data = request()->validate([
            'patients' => 'required|array',
        ]);

        collect($data['patients'])
            ->filter()
            ->each(function ($patientId) {
                Patient::find($patientId)->validateOwnership(Auth::user()->current_team_id);
                PatientSelector::remove($patientId);
            });

        return back();
    }
}
