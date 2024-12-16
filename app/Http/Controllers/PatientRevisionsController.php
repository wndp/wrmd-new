<?php

namespace App\Http\Controllers;

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
            'media.revisions',
            'banding.revisions',
            'exams.revisions',
            'expenses.revisions',
            'labs.revisions',
            'locations.revisions',
            'necropsy.revisions',
            'morphometric.revisions',
            'eventProcessing.revisions',
            //'eventPrewash.revisions',
            'eventWash.revisions',
            'eventConditioning.revisions',
            'prescriptions.revisions',
            'rechecks.revisions',
            'rescuer.revisions',
            'treatmentLogs.revisions',
            'wildlifeRecovery.revisions',
            'customValues.revisions',
        ];

        $relations = collect($admission->patient->load('admissions.account')->load($patientRelations)->getRelations())->flatMap(function ($collection) {
            $collection = ! $collection instanceof Collection ? collect([$collection]) : $collection;

            return $collection->filter()->flatMap(function ($model) {
                return $model->revisions;
            });
        });

        $revisions = $admission->patient->revisions->merge($relations)->sortBy('created_at')->values();

        return Inertia::render('Revisions/Index', compact('revisions'));
    }

    /**
     * Display the view for the specified patient and revision.
     */
    public function show(Revision $revision)
    {
        $admission = $this->loadAdmissionAndSharePagination();

        abort_unless($this->belongsToAdmission($revision, $admission), new RecordNotOwnedResponse);

        $revision->append('diff', 'updated_attributes');

        return Inertia::render('Revisions/Show', compact('revision'));
    }

    /**
     * Determine if the revision belongs to the admission.
     *
     * @return bool
     */
    public function belongsToAdmission(Revision $revision, Admission $admission)
    {
        return ($revision->revisionable instanceof Patient && (int) $revision->revisionable_id === (int) $admission->patient_id)
            || ($revision->revisionable->patient instanceof Patient && (int) $revision->revisionable->patient->id === (int) $admission->patient_id)
            || ($revision->revisionable->patients instanceof Collection && $revision->revisionable->patients->containsStrict('id', $admission->patient_id));
    }
}
