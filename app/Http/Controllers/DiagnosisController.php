<?php

namespace App\Http\Controllers;

use App\Domain\Patients\Patient;
use App\Events\PatientUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiagnosisController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_account_id)
            ->update($request->only([
                'diagnosis',
            ]));

        event(new PatientUpdated($patient));

        return back();
    }
}
