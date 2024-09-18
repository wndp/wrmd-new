<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Accounts\Account;
use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\TaxaPreciseSearchQuery;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UnrecognizedAccountPatientController extends Controller
{
    /**
     * Identify a patient's unknown species.
     */
    public function __invoke(Account $account = null): JsonResponse
    {
        $data = request()->validate([
            'taxon_id' => 'required|integer',
            'oldCommonName' => 'required',
            'newCommonName' => 'required',
        ]);

        $results = (new TaxaPreciseSearchQuery())($data['newCommonName']);

        abort_unless(
            $results->unique('species_id')->count() === 1,
            response()->json([
                'success' => false,
                'message' => "Unknown common name [{$data['newCommonName']}].",
                'data' => [],
            ], 422)
        );

        Patient::select('patients.id')
            ->join('admissions', 'patients.id', '=', 'admissions.patient_id')
            ->where([
                'account_id' => ($account ?: Auth::user()->currentAccount)->id,
                'taxon_id' => $data['taxon_id'],
                'common_name' => $data['oldCommonName'],
            ])->get()->each(function ($patient) use ($data, $results) {
                $patient->timestamps = false;
                $patient->taxon_id = $results->first()['taxon_id'];
                $patient->common_name = $data['newCommonName'];
                $patient->save();
            });

        return response()->json([
            'success' => true,
            'message' => "Patient's common name updated.",
            'data' => [],
        ]);
    }
}
