<?php

namespace App\Http\Controllers\Sharing;

use App\Http\Controllers\Controller;
use App\Jobs\ShareMedicalRecords;
use App\Models\Admission;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    /**
     * Produce an admission or collection of admissions into PDF medical record.
     */
    public function store(Request $request, ?Patient $patient = null): RedirectResponse
    {
        $request->validate([
            'to' => 'required',
            'bcc_me' => 'nullable|boolean',
            'subject' => 'required',
            'body' => 'required',
        ]);

        $admissions = $patient instanceof Patient
            ? Admission::where(['team_id' => Auth::user()->current_team_id, 'patient_id' => $patient->id])->get()
            : Admission::where('team_id', Auth::user()->current_team_id)
                ->limitToSelected()
                ->get();

        //Admission::scopedList(Auth::user()->currentAccount->id)->limitToSelected()->get();

        ShareMedicalRecords::dispatch(
            Auth::user()->currentTeam,
            Auth::user(),
            $admissions->pluck('patient_id')->toArray(),
            $request->all(),
            $request->cookie('device-uuid'),
            'email'
        );

        return redirect()->back()
            ->with('notification.heading', __('Email Sent!'))
            ->with('notification.text', __('The patient medical record has been sent.'));
    }
}
