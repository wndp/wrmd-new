<?php

namespace App\Http\Controllers\Maintenance;

use App\Domain\Admissions\Admission;
use App\Events\AccountUpdated;
use App\Events\PatientDeleted;
use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
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
        ExtensionNavigation::emit('maintenance');

        $yearsInAccount = Admission::yearsInAccount(Auth::user()->currentAccount->id)->toArray();

        return Inertia::render('Maintenance/DeletePatient', compact('yearsInAccount'));
    }

    /**
     * Attempt to delete the patient from the specified year.
     */
    public function destroy(): RedirectResponse
    {
        $account = auth()->user()->currentAccount;

        $data = request()->validate([
            'year' => 'required|integer|in:'.Admission::yearsInAccount($account->id)->implode(','),
            'password' => ['required', 'confirmed', 'current_password'],
        ]);

        $admission = Admission::query()
            ->where([
                'account_id' => $account->id,
                'case_year' => $data['year'],
            ])
            ->orderByDesc('case_id')
            ->limit(1)
            ->first();

        if ($admission instanceof Admission) {
            Admission::query()
                ->where([
                    'account_id' => $account->id,
                    'case_year' => $data['year'],
                ])
                ->orderByDesc('case_id')
                ->limit(1)
                ->delete();

            event(new AccountUpdated($account));
            event(new PatientDeleted($admission->patient));

            Metadata::add(
                $account->id,
                'deletion',
                'deleted_patient',
                "{$admission->caseNumber} deleted by ".Auth::user()->name
            );

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
