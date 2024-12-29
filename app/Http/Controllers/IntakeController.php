<?php

namespace App\Http\Controllers;

use App\Events\PatientUpdated;
use App\Http\Requests\SaveIntakeRequest;
use App\Models\Patient;
use App\ValueObjects\SingleStorePoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntakeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(SaveIntakeRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id)
            ->update($request->only([
                'transported_by',
                'admitted_by',
                'found_at',
                'address_found',
                'city_found',
                'county_found',
                'subdivision_found',
                'reason_for_admission',
                'care_by_rescuer',
                'notes_about_rescue',
            ]));

        if ($request->filled('latitude_found', 'longitude_found')) {
            $patient->coordinates_found = new SingleStorePoint($request->input('latitude_found'), $request->input('longitude_found'));
            $patient->save();
        }

        event(new PatientUpdated($patient));

        return back();
    }
}
