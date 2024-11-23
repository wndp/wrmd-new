<?php

namespace App\Http\Controllers\Oiled;

use App\Enums\Extension;
use App\Http\Controllers\Controller;
use App\Models\OilProcessing;
use App\Models\Patient;
use App\Support\ExtensionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcessingCommentsController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $request->validate([
            'comments' => 'nullable|string',
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id);

        OilProcessing::store(
            $patient->id,
            request()->only([
                'comments',
            ]),
            isIndividualOiledAnimal: ExtensionManager::isActivated(Extension::OWCN_MEMBER_ORGANIZATION, Auth::user()->currentTeam)
        );

        return back();
    }
}
