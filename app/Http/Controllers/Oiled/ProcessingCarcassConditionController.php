<?php

namespace App\Http\Controllers\Oiled;

use App\Enums\AttributeOptionName;
use App\Enums\Extension;
use App\Http\Controllers\Controller;
use App\Models\OilProcessing;
use App\Models\Patient;
use App\Rules\AttributeOptionExistsRule;
use App\Support\ExtensionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcessingCarcassConditionController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $request->validate([
            'carcass_condition_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_CARCASS_CONDITIONS),
            ],
            'extent_of_scavenging_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_EXTENT_OF_SCAVENGINGS),
            ],
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id);

        OilProcessing::store(
            $patient->id,
            request()->only([
                'carcass_condition_id',
                'extent_of_scavenging_id',
            ]),
            isIndividualOiledAnimal: ExtensionManager::isActivated(Extension::OWCN_MEMBER_ORGANIZATION, Auth::user()->currentTeam)
        );

        return back();
    }
}
