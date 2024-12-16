<?php

namespace App\Http\Controllers;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Events\ExamUpdated;
use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use App\Models\Patient;

class IntakeExamController extends Controller
{
    public function __invoke(ExamRequest $request, Patient $patient)
    {
        [$intakeExamTypeId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::EXAM_TYPES->value,
            AttributeOptionUiBehavior::EXAM_TYPE_IS_INTAKE->value,
        ]);

        Exam::updateOrCreate(
            ['patient_id' => $patient->id, 'exam_type_id' => $intakeExamTypeId],
            $request->dataFromRequest()
        );

        //event(new ExamUpdated($exam));

        return back();
    }
}
