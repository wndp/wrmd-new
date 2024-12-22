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

class DeleteYearController extends Controller
{
    /**
     * Display the view to delete all patients in a year.
     */
    public function index(): Response
    {
        $yearsInAccount = Admission::yearsInAccount(Auth::user()->currentTeam->id)->toArray();

        return Inertia::render('Maintenance/DeleteYear', compact('yearsInAccount'));
    }

    /**
     * Attempt to delete all patients from the specified year.
     */
    public function destroy(): RedirectResponse
    {
        $team = Auth::user()->currentTeam;

        $data = request()->validate([
            'year' => 'required|integer|in:'.Admission::yearsInAccount($team->id)->implode(','),
            'password' => ['required', 'confirmed', 'current_password'],
        ]);

        $admissions = Admission::where([
            'team_id' => $team->id,
            'case_year' => $data['year'],
        ])
            ->get();

        if (! $admissions->isEmpty()) {
            Admission::where([
                'team_id' => $team->id,
                'case_year' => $data['year'],
            ])->delete();

            // event(new AccountUpdated($team));
            // event(new PatientDeleted($admissions->first()->patient));

            // Metadata::add(
            //     $team->id,
            //     'deletion',
            //     'deleted_year',
            //     "{$data['year']} deleted by ".auth()->user()->name
            // );

            return redirect()->route('year.delete.index')
                ->with('flash.notificationHeading', 'Patient Deleted')
                ->with('flash.notification', "All patients for {$data['year']} were deleted.")
                ->with('flash.notificationStyle', 'success');
        }

        return redirect()->route('patient.delete.index')
            ->with('flash.notificationHeading', 'Oops!')
            ->with('flash.notification', "There was a problem deleting the patients from {$data['year']}")
            ->with('flash.notificationStyle', 'danger');
    }
}
