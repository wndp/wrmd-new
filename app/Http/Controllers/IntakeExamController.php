<?php

namespace App\Http\Controllers;

use App\Domain\OptionsStore;
use App\Domain\Patients\Exam;
use App\Domain\Patients\ExamOptions;
use App\Domain\Patients\Patient;
use App\Events\ExamUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExamRequest;
use Inertia\Inertia;

class IntakeExamController extends Controller
{
    public function __invoke(ExamRequest $request, Patient $patient)
    {
        $exam = tap(
            Exam::getIntakeExam($patient->id),
            fn ($exam) => $exam->fill($request->dataFromRequest())->save()
        );

        event(new ExamUpdated($exam));

        return back();
    }
}
