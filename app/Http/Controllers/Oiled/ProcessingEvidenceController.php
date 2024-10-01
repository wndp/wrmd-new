<?php

namespace App\Http\Controllers\Oiled;

use App\Enums\AttributeOptionName;
use App\Enums\Extension;
use App\Http\Controllers\Controller;
use App\Models\OilProcessing;
use App\Models\Patient;
use App\Rules\AttributeOptionExistsRule;
use App\Support\ExtensionManager;
use App\Support\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcessingEvidenceController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $request->validate([
            'evidence_collected' => 'nullable|array',
            'evidence_collected.*' => [
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_EVIDENCES),
            ],
            'evidence_collected_by' => 'required_with:evidence_collected,processed_at',
            'processed_at' => 'nullable|required_with:evidence_collected,evidence_collected_by|date',
        ], [
            'evidence_collected_by.required_with' => 'The collected by field is required if evidence is provided.',
            'processed_at.required_with' => 'The date processed field is required if evidence is provided.',
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id);
        $processedAt = Timezone::convertFromLocalToUtc($request->input('processed_at'));

        OilProcessing::store(
            $patient->id,
            [
                'evidence_collected' => $request->input('evidence_collected'),
                'evidence_collected_by' => $request->input('evidence_collected_by'),
                'date_processed_at' => $processedAt->toDateString(),
                'time_processed_at' => $processedAt->toTimeString(),
            ],
            isIndividualOiledAnimal: ExtensionManager::isActivated(Extension::OWCN_MEMBER_ORGANIZATION, Auth::user()->currentTeam)
        );

        return back();
    }
}
