<?php

namespace App\Http\Controllers\Maintenance;

use App\Events\AccountUpdated;
use App\Events\PatientDeleted;
use App\Http\Controllers\Controller;
use App\Models\Admission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Metadata;

class DeletePatientController extends Controller
{
    /**
     * Display the view to delete a single patient.
     */
    public function index(): Response
    {
        $yearsInAccount = Admission::yearsInAccount(Auth::user()->currentTeam->id)->toArray();

        return Inertia::render('Maintenance/DeletePatient', compact('yearsInAccount'));
    }

    /**
     * Attempt to delete the patient from the specified year.
     */
    public function destroy(): RedirectResponse
    {
        $team = Auth::user()->currentTeam;

        $data = request()->validate([
            'year' => 'required|integer|in:'.Admission::yearsInAccount($team->id)->implode(','),
            'password' => ['required', 'confirmed', 'current_password'],
        ]);

        $admission = Admission::query()
            ->where([
                'team_id' => $team->id,
                'case_year' => $data['year'],
            ])
            ->orderByDesc('case_id')
            ->limit(1)
            ->first();

        if ($admission instanceof Admission) {
            Admission::query()
                ->where('id', $admission->id)
                ->limit(1)
                ->delete();

            // event(new AccountUpdated($team));
            // event(new PatientDeleted($admission->patient));

            // Metadata::add(
            //     $team->id,
            //     'deletion',
            //     'deleted_patient',
            //     "{$admission->caseNumber} deleted by ".Auth::user()->name
            // );

            return redirect()->route('patient.delete.index')
                ->with('flash.notificationHeading', 'Patient Deleted')
                ->with('flash.notification', "Patient $admission->caseNumber was deleted.")
                ->with('flash.notificationStyle', 'success');
        }

        return redirect()->route('patient.delete.index')
            ->with('flash.notificationHeading', 'Oops!')
            ->with('flash.notification', "There was a problem deleting the last patient from {$data['year']}")
            ->with('flash.notificationStyle', 'danger');
    }
}
