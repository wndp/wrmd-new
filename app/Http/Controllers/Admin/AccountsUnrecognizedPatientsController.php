<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Accounts\Account;
use App\Domain\Admissions\Admission;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AccountsUnrecognizedPatientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Account $account): Response
    {
        $unrecognizedPatients = Admission::select('admissions.*')
            ->where('account_id', $account->id)
            ->whereUnrecognized()
            ->orderBy('common_name')
            ->with('patient', 'account')
            ->paginate()
            ->onEachSide(1);

        return Inertia::render('Admin/Accounts/Unrecognized', compact('account', 'unrecognizedPatients'));
    }
}
