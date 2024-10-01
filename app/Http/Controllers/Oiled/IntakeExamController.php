<?php

namespace App\Http\Controllers\Oiled;

use App\Domain\OptionsStore;
use App\Domain\Patients\ExamOptions;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IntakeExamController extends Controller
{
    public function __invoke(ExamOptions $examOptions): Response
    {
        OptionsStore::merge($examOptions);

        $admission = $this->loadAdmissionAndSharePagination();
        $admission->patient->load('eventProcessing');
        $exam = $admission->patient->intake_exam;

        return Inertia::render('Oiled/IntakeExam', compact('exam'));
    }
}
