<?php

namespace App\Http\Controllers\Sharing;

use App\Http\Controllers\Controller;
use App\Jobs\ShareMedicalRecords;
use App\Models\Admission;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintController extends Controller
{
    /**
     * Produce an admission or collection of admissions into a PDF medical record.
     *
     * @param  \Illuminate\Http\Request request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Patient $patient = null)
    {
        $result = $patient instanceof Patient
            ? Admission::where(['team_id' => Auth::user()->currentTeam->id, 'patient_id' => $patient->id])->get()
            : Admission::where('team_id', Auth::user()->currentTeam->id)
                ->selectColumns('*')
                ->with('patient')
                ->limitToSearchResults()
                ->joinTables('*')
                ->selectAdmissionKeys()
                ->orderBy('admissions.case_year')
                ->orderBy('admissions.id')
                ->limitToSelected()
                ->get();

        ShareMedicalRecords::dispatch(
            Auth::user()->currentTeam,
            Auth::user(),
            $result->pluck('patient_id')->toArray(),
            $request->all(),
            $request->cookie('device-uuid'),
            'pdf'
        );

        return back();
    }
}
