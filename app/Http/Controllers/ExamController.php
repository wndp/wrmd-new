<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = $this->loadAdmissionAndSharePagination()->patient->exams;

        return Inertia::render('Patients/Exams/Index', compact('exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ExamOptions $options)
    {
        $exams = $this->loadAdmissionAndSharePagination()->patient->exams;

        $this->transformAndShareOptions($options, $exams);

        return Inertia::render('Patients/Exams/Create');
    }

    /**
     * Save an exam in storage.
     */
    public function store(ExamRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $exam = tap(new Exam($request->dataFromRequest()), function ($exam) use ($patient) {
            $exam->patient_id = $patient->id;
            $exam->save();
        });

        $admission = Admission::custody(Auth::user()->currentAccount, $patient);

        event(new ExamUpdated($exam));

        return redirect()->route('patients.exam.index', [
            'y' => $admission->case_year,
            'c' => $admission->case_id,
        ], 303);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit(ExamOptions $options, Request $request, Patient $patient, Exam $exam)
    {
        $exam->validateOwnership(Auth::user()->current_team_id);

        $exams = $this->loadAdmissionAndSharePagination()->patient->exams;

        $this->transformAndShareOptions($options, $exams, $exam);

        return Inertia::render('Patients/Exams/Edit', compact('exam'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExamRequest $request, Patient $patient, Exam $exam)
    {
        $exam->validateOwnership(Auth::user()->current_team_id);
        $exam->update($request->dataFromRequest());

        event(new ExamUpdated($exam));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient, Exam $exam)
    {
        $exam->validateOwnership(Auth::user()->current_team_id)->delete();

        $admission = Admission::custody(Auth::user()->currentAccount, $patient);

        return redirect()
            ->route('patients.exam.index', [
                'y' => $admission->case_year,
                'c' => $admission->case_id,
            ], 303)
            ->with('flash.notificationHeading', 'Exam Deleted')
            ->with('flash.notification', "$exam->type exam on $exam->examined_at_for_humans was deleted.");
    }

    /**
     * Validate the request.
     *
     * @return void
     */
    public function validateRequest(Request $request, Patient $patient, $exam = null)
    {
        $request->validate([
            'type' => ['required', Rule::in(ExamOptions::$types)],
            'examined_at' => 'required|date|after_or_equal:'.$patient->admitted_at->setTimezone(settings('timezone')),
            'examiner' => 'required',
        ], [
            'type.required' => 'The exam type field is required.',
            'type.in' => 'The selected exam type must be in ['.implode(', ', ExamOptions::$types).'].',
            'examined_at.required' => 'The examined at date field is required.',
            'examined_at.date' => 'The examined at date is not a valid date.',
            'examiner.required' => 'The examiner field is required.',
        ]);

        if (in_array($request->type, ['Intake', 'Release'])) {
            $request->validate([
                'type' => Rule::unique('exams')
                    ->where('patient_id', $patient->id)
                    ->where('type', $request->type)
                    ->ignore($exam ?? 'NULL'),
            ], [
                'type.unique' => "Only 1 {$request->type} exam can be created.",
            ]);
        }
    }

    // public function dataFromRequest(Request $request): array
    // {
    //     return array_merge([
    //         'examined_at' => $request->convertDateFromLocal('examined_at'),
    //     ], $request->only([
    //         'type',
    //         'sex',
    //         'weight',
    //         'weight_unit',
    //         'bcs',
    //         'age',
    //         'age_unit',
    //         'attitude',
    //         'dehydration',
    //         'temperature',
    //         'temperature_unit',
    //         'mm_color',
    //         'mm_texture',
    //         'head',
    //         'cns',
    //         'cardiopulmonary',
    //         'gastrointestinal',
    //         'musculoskeletal',
    //         'integument',
    //         'body',
    //         'forelimb',
    //         'hindlimb',
    //         'head_finding',
    //         'cns_finding',
    //         'cardiopulmonary_finding',
    //         'gastrointestinal_finding',
    //         'musculoskeletal_finding',
    //         'integument_finding',
    //         'body_finding',
    //         'forelimb_finding',
    //         'hindlimb_finding',
    //         'treatment',
    //         'fluids_nutrition',
    //         'comments',
    //         'examiner',
    //     ]));
    // }

    public function transformAndShareOptions($options, $exams, $exam = null)
    {
        $availableExamTypes = $options::$types;

        if ($exams->where('type', 'Intake')->isNotEmpty() && ($exam?->type !== 'Intake')) {
            Arr::forget($availableExamTypes, array_search('Intake', $availableExamTypes));
        }

        if ($exams->where('type', 'Release')->isNotEmpty() && ($exam?->type !== 'Release')) {
            Arr::forget($availableExamTypes, array_search('Release', $availableExamTypes));
        }

        OptionsStore::merge($options);
        OptionsStore::merge(['availableExamTypes' => array_values($availableExamTypes)]);
    }
}
