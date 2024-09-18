<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Accounts\SpecialtyAccounts;
use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\TaxaPreciseSearchQuery;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class UnrecognizedPatientController extends Controller
{
    /**
     * Attempt to identify a patient's unrecognized species.
     */
    public function __invoke(Patient $patient): JsonResponse
    {
        $data = request()->validate([
            'newCommonName' => 'required',
        ]);

        if ((int) auth()->user()->currentAccount->id !== SpecialtyAccounts::WRMD) {
            $patient->validateOwnership(auth()->user()->currentAccount->id);
        }

        $results = (new TaxaPreciseSearchQuery())($data['newCommonName']);

        abort_if(
            $results->unique('taxon_id')->count() !== 1,
            response()->json([
                'success' => false,
                'message' => "Unknown common name [{$data['newCommonName']}].",
                'data' => [],
            ], 422)
        );

        $patient->timestamps = false;
        $patient->taxon_id = $results->first()['taxon_id'];
        $patient->common_name = $data['newCommonName'];
        $patient->save();

        return response()->json([
            'success' => true,
            'message' => "Patient's common name updated.",
            'data' => [],
        ]);
    }
}
