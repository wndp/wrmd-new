<?php

namespace App\Http\Controllers\Necropsy;

use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Models\Necropsy;
use App\Models\Patient;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NecropsySystemsController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $data = $request->validate([
            'integument_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'integument' => [
                'nullable',
                'string',
            ],
            'cavities_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'cavities' => [
                'nullable',
                'string',
            ],
            'gastrointestinal_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'gastrointestinal' => [
                'nullable',
                'string',
            ],
            'liver_gallbladder_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'liver_gallbladder' => [
                'nullable',
                'string',
            ],
            'hematopoietic_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'hematopoietic' => [
                'nullable',
                'string',
            ],
            'renal_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'renal' => [
                'nullable',
                'string',
            ],
            'respiratory_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'respiratory' => [
                'nullable',
                'string',
            ],
            'cardiovascular_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'cardiovascular' => [
                'nullable',
                'string',
            ],
            'endocrine_reproductive_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'endocrine_reproductive' => [
                'nullable',
                'string',
            ],
            'nervous_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'nervous' => [
                'nullable',
                'string',
            ],
            'head_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'head' => [
                'nullable',
                'string',
            ],
            'musculoskeletal_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'musculoskeletal' => [
                'nullable',
                'string',
            ],
        ]);

        Necropsy::updateOrCreate(['patient_id' => $patient->id], [
            'integument_finding_id' => $request->input('integument_finding_id'),
            'integument' => $request->input('integument'),
            'cavities_finding_id' => $request->input('cavities_finding_id'),
            'cavities' => $request->input('cavities'),
            'gastrointestinal_finding_id' => $request->input('gastrointestinal_finding_id'),
            'gastrointestinal' => $request->input('gastrointestinal'),
            'liver_gallbladder_finding_id' => $request->input('liver_gallbladder_finding_id'),
            'liver_gallbladder' => $request->input('liver_gallbladder'),
            'hematopoietic_finding_id' => $request->input('hematopoietic_finding_id'),
            'hematopoietic' => $request->input('hematopoietic'),
            'renal_finding_id' => $request->input('renal_finding_id'),
            'renal' => $request->input('renal'),
            'respiratory_finding_id' => $request->input('respiratory_finding_id'),
            'respiratory' => $request->input('respiratory'),
            'cardiovascular_finding_id' => $request->input('cardiovascular_finding_id'),
            'cardiovascular' => $request->input('cardiovascular'),
            'endocrine_reproductive_finding_id' => $request->input('endocrine_reproductive_finding_id'),
            'endocrine_reproductive' => $request->input('endocrine_reproductive'),
            'nervous_finding_id' => $request->input('nervous_finding_id'),
            'nervous' => $request->input('nervous'),
            'head_finding_id' => $request->input('head_finding_id'),
            'head' => $request->input('head'),
            'musculoskeletal_finding_id' => $request->input('musculoskeletal_finding_id'),
            'musculoskeletal' => $request->input('musculoskeletal'),
        ]);

        return back();
    }
}
