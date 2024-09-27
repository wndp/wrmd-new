<?php

namespace App\Http\Controllers\Necropsy;

use App\Domain\Patients\Patient;
use App\Extensions\Necropsy\Necropsy;
use App\Http\Controllers\Controller;
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

        $necropsy = Necropsy::firstOrNew(['patient_id' => $patient->id]);
        $necropsy->patient_id = $patient->id;
        $necropsy->fill($data);
        $necropsy->save();

        return back();
    }
}
