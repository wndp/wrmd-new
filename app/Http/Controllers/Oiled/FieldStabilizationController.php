<?php

namespace App\Http\Controllers\Oiled;

use App\Domain\OptionsStore;
use App\Domain\Patients\Exam;
use App\Domain\Patients\ExamOptions;
use App\Domain\Patients\Patient;
use App\Events\ExamUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExamRequest;
use Inertia\Inertia;

class FieldStabilizationController extends Controller
{
    public function edit(ExamOptions $examOptions)
    {
        OptionsStore::merge($examOptions);

        $admission = $this->loadAdmissionAndSharePagination();
        $admission->patient->load('eventProcessing');
        $exam = $admission->patient->field_exam;

        return Inertia::render('Oiled/FieldStabilization', compact('exam'));
    }

    public function update(ExamRequest $request, Patient $patient)
    {
        $exam = tap(
            Exam::getFieldExam($patient->id),
            fn ($exam) => $exam->fill($request->dataFromRequest())->save()
        );

        event(new ExamUpdated($exam));

        return back();
    }
}
