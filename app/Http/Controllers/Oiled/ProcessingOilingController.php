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
            'processor' => 'required|string',
            'oiling_status' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_STATUSES),
            ],
            'oiling_percentage' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_OILING_PERCENTAGES),
            ],
            'oiling_depth' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_OILING_DEPTHS),
            ],
            'oiling_location' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_OILING_LOCATIONS),
            ],
            'type_of_oil' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_OIL_TYPES),
            ],
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id);

        OilProcessing::store(
            $patient->id,
            [
                'processor' => $request->input('processor'),
                'oiling_status_id' => $request->input('oiling_status_id'),
                'oiling_percentage_id' => $request->input('oiling_percentage_id'),
                'oiling_depth_id' => $request->input('oiling_depth_id'),
                'oiling_location_id' => $request->input('oiling_location_id'),
                'type_of_oil_id' => $request->input('type_of_oil_id'),
            ],
            isIndividualOiledAnimal: ExtensionManager::isActivated(Extension::OWCN_MEMBER_ORGANIZATION, Auth::user()->currentTeam)
        );

        return back();
    }
}
