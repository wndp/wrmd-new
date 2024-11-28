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

class ProcessingOilingController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $request->validate([
            'processed_by' => 'required|string',
            'oiling_status_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_STATUSES),
            ],
            'oiling_percentage_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_OILING_PERCENTAGES),
            ],
            'oiling_depth_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_OILING_DEPTHS),
            ],
            'oiling_location_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_OILING_LOCATIONS),
            ],
            'color_of_oil_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_OIL_COLORS),
            ],
            'oil_condition_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_OIL_CONDITIONS),
            ],
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id);

        OilProcessing::store(
            $patient->id,
            [
                'processed_by' => $request->input('processed_by'),
                'oiling_status_id' => $request->input('oiling_status_id'),
                'oiling_percentage_id' => $request->input('oiling_percentage_id'),
                'oiling_depth_id' => $request->input('oiling_depth_id'),
                'oiling_location_id' => $request->input('oiling_location_id'),
                'color_of_oil_id' => $request->input('color_of_oil_id'),
                'oil_condition_id' => $request->input('oil_condition_id'),
            ],
            isIndividualOiledAnimal: ExtensionManager::isActivated(Extension::OWCN_MEMBER_ORGANIZATION, Auth::user()->currentTeam)
        );

        return back();
    }
}
