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
use MatanYadaev\EloquentSpatial\Objects\Point;

class ProcessingReceivingController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $request->validate([
            'received_at_primary_care_at' => 'required|date',
            'received_at_primary_care_by' => 'required|string',
            'species_confirmed_by' => 'nullable|string'
        ], [
            'received_at_primary_care_at.required' => 'The date received field is required.',
            'received_at_primary_care_at.date' => 'The date received field is not a valid date.',
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id);

        $collectedAt = Timezone::convertFromLocalToUtc($request->input('received_at_primary_care_at'));

        OilProcessing::store(
            $patient->id,
            [
                'date_received_at_primary_care_at' => $collectedAt->toDateString(),
                'time_received_at_primary_care_at' => $collectedAt->toTimeString(),
                'received_at_primary_care_by' => $request->input('received_at_primary_care_by'),
                'species_confirmed_by' => $request->input('species_confirmed_by'),
            ],
            isIndividualOiledAnimal: ExtensionManager::isActivated(Extension::OWCN_MEMBER_ORGANIZATION, Auth::user()->currentTeam)
        );

        return back();
    }
}
