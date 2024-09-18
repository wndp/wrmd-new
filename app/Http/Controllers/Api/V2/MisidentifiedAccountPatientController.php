<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Accounts\Account;
use App\Domain\Admissions\Admission;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MisidentifiedAccountPatientController extends Controller
{
    /**
     * Get all of the misidentified patients.
     */
    public function index(Account $account): JsonResponse
    {
        return response()->json(
            Admission::select('admissions.*')
                ->whereMisidentified()
                ->where('account_id', $account->id)
                ->orderBy('common_name')
                ->with('patient', 'account')
                ->paginate(500)
        );
    }
}
