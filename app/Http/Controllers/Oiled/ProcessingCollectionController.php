<?php

namespace App\Http\Controllers\Oiled;

use App\ValueObjects\SingleStorePoint;
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

class ProcessingCollectionController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $request->validate([
            'collected_at' => 'required|date',
            'collection_condition_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_PROCESSING_CONDITIONS),
            ],
        ], [
            'collected_at.required' => 'The date collected field is required.',
            'collected_at.date' => 'The date collected field is not a valid date.',
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id);

        $patient->address_found = $request->input('address_found');
        $patient->coordinates_found = $request->filled('lat_found', 'lng_found') ? new SingleStorePoint($request->input('lat_found'), $request->input('lng_found')) : null;
        $patient->save();

        $patient->rescuer->update([
            'first_name' => $request->input('collector_name'),
        ]);

        $collectedAt = Timezone::convertFromLocalToUtc($request->input('collected_at'));

        OilProcessing::store(
            $patient->id,
            [
                'date_collected_at' => $collectedAt->toDateString(),
                'time_collected_at' => $collectedAt->toTimeString(),
                'collection_condition_id' => $request->input('collection_condition_id'),
            ],
            isIndividualOiledAnimal: ExtensionManager::isActivated(Extension::OWCN_MEMBER_ORGANIZATION, Auth::user()->currentTeam)
        );

        return back();
    }
}
