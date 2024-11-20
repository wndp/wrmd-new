<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Accounts\Account;
use App\Domain\Admissions\Admission;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AccountsMisidentifiedPatientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Account $account): Response
    {
        $misidentifiedPatients = Admission::select('admissions.*')
            ->where('account_id', $account->id)
            ->whereMisidentified()
            ->orderBy('common_name')
            ->with('patient', 'account')
            ->paginate()
            ->onEachSide(1);

        return Inertia::render('Admin/Accounts/Misidentified', compact('account', 'misidentifiedPatients'));
    }
}
