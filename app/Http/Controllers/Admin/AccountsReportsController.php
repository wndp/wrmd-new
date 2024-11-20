<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Accounts\Account;
use App\Domain\Reporting\ReportsCollection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AccountsReportsController extends Controller
{
    public function index(): Response
    {
        $accountReports = app(ReportsCollection::class)->firstWhere('title', 'Account Reports');

        // ->instantiate(
        //     Auth::user()->current_account
        // )

        return Inertia::render('Admin/Accounts/AccountReports', compact('accountReports'));
    }
}
