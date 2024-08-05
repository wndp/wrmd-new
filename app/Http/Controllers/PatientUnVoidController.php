<?php

namespace App\Http\Controllers;

use App\Domain\Patients\Patient;
use App\Events\PatientUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientUnVoidController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Patient $voidedPatient)
    {
        $voidedPatient->validateOwnership(Auth::user()->current_account_id)->unVoid();

        event(new PatientUpdated($voidedPatient));

        return back();
    }
}
