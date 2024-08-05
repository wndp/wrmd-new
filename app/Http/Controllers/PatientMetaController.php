<?php

namespace App\Http\Controllers;

use App\Domain\Patients\Patient;
use App\Events\PatientUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientMetaController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_account_id);

        $data = $request->validate([
            'is_frozen' => 'required|boolean',
            'is_voided' => 'required|boolean',
            'is_resident' => 'required|boolean',
            'criminal_activity' => 'required|boolean',
            'keywords' => 'nullable|string',
        ]);

        if ($request->boolean('is_frozen')) {
            $patient->unLock();
        }

        $patient->update($data);

        event(new PatientUpdated($patient));

        return back();
    }
}
