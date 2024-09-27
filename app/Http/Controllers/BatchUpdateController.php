<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BatchUpdateController extends Controller
{
    /**
     * Display the form for batch updating multiple records.
     */
    public function edit(ExamOptions $options)
    {
        OptionsStore::merge($options);

        if (! $this->canBatch()) {
            return redirect()->route('search.simple.create')
                ->with('flash.notificationHeading', __('Oops!'))
                ->with('flash.notification', __('You must first search for or select patients before batch updating.'))
                ->with('flash.notificationStyle', 'danger');
        }

        $patientIds = $this->getPatientIds();

        $caseNumbers = Admission::owner(Auth::user()->current_team_id)
            ->whereIn('patient_id', $patientIds)
            ->get()
            ->pluck('caseNumber');

        return Inertia::render('Patients/Batch', compact('caseNumbers'));
    }

    /**
     * Batch update the specified cases in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'how_should_text_values_be_updated' => 'required|in:0,1',
        ]);

        BatchHandler::validate($request);

        $success = BatchHandler::run(
            $this->getPatientIds(),
            $request,
            $request->boolean('how_should_text_values_be_updated')
        );

        if ($success) {
            PatientSelector::empty();
            QueryCache::empty();

            return redirect()->route('patients.index')
                ->with('flash.notificationHeading', __('Success!'))
                ->with('flash.notification', __('Your patients have been batch updated.'));
        }

        return redirect()->route('patients.index')
            ->with('flash.notificationHeading', __('Opps!'))
            ->with('flash.notification', __('There was a problem batch updating your patients.'))
            ->with('flash.notificationStyle', 'danger');
    }

    /**
     * Determine if the user can do batch updating.
     */
    private function canBatch(): bool
    {
        return PatientSelector::exists() || QueryCache::exists();
    }

    /**
     * Get the patient IDs to batch update.
     */
    private function getPatientIds(): array
    {
        if (PatientSelector::exists()) {
            return PatientSelector::get();
        }

        if (SearchCache::exists()) {
            return SearchCache::get()->patientIds;
        }

        return [];
    }
}
