<?php

namespace App\Http\Controllers;

use App\Exceptions\RecordNotOwned;
use App\Models\Admission;
use App\Models\Patient;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class PatientRevisionsController extends Controller
{
    /**
     * Display the list of a patients revisions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admission = $this->loadAdmissionAndSharePagination();

        $patientRelations = [
            'media.activities',
            'banding.activities',
            'exams.activities',
            'expenseTransactions.activities',
            'labReports.activities',
            'locations.activities',
            'necropsy.activities',
            'morphometric.activities',
            'oilProcessing.activities',
            //'eventPrewash.activities',
            'oilWashes.activities',
            'oilWaterproofingAssessments.activities',
            'prescriptions.activities',
            'rechecks.activities',
            'rescuer.activities',
            'careLogs.activities',
            'wildlifeRecovery.activities',
            'customValues.activities',
        ];

        $relations = collect($admission->patient->load('admissions.team')->load($patientRelations)->getRelations())->flatMap(function ($collection) {
            $collection = ! $collection instanceof Collection ? collect([$collection]) : $collection;

            return $collection->filter()->flatMap(function ($model) {
                return $model->revisions;
            });
        });

        $activities = $admission->patient->activities->merge($relations)->sortBy('created_at')->values();

        return Inertia::render('Patients/Revisions/Index', compact('activities'));
    }

    /**
     * Display the view for the specified patient and revision.
     */
    public function show(Activity $activity)
    {
        $admission = $this->loadAdmissionAndSharePagination();

        abort_unless($this->belongsToAdmission($activity, $admission), new RecordNotOwned);

        //$activity->append('diff', 'updated_attributes');

        return Inertia::render('Patients/Revisions/Show', compact('activity'));
    }

    /**
     * Determine if the revision belongs to the admission.
     *
     * @return bool
     */
    public function belongsToAdmission(Activity $activity, Admission $admission)
    {
        return ($activity->subject instanceof Patient && (int) $activity->subject_id === (int) $admission->patient_id)
            || ($activity->subject->patient instanceof Patient && (int) $activity->subject->patient->id === (int) $admission->patient_id)
            || ($activity->subject->patients instanceof Collection && $activity->subject->patients->containsStrict('id', $admission->patient_id));
    }
}
