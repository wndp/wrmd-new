<?php

namespace App\Http\Controllers;

use App\Events\PatientUpdated;
use App\Models\Patient;
use Carbon\Carbon;
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
        $patient->validateOwnership(Auth::user()->current_team_id);

        $data = $request->validate([
            'is_locked' => 'required|boolean',
            'is_voided' => 'required|boolean',
            'is_resident' => 'required|boolean',
            'is_criminal_activity' => 'required|boolean',
            'keywords' => 'nullable|string',
        ]);

        // if ($request->boolean('is_locked')) {
        //     $patient->unLock();
        // }

        $patient->update([
            'locked_at' => $data['is_locked'] ? Carbon::now() : null,
            'voided_at' => $data['is_voided'] ? Carbon::now() : null,
            'is_resident' => $data['is_resident'],
            'is_criminal_activity' => $data['is_criminal_activity'],
            'keywords' => $data['keywords'] ?? null,
        ]);

        event(new PatientUpdated($patient));

        return back();
    }
}
