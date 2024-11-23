<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Reporting\ReportsCollection;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AccountsReportsController extends Controller
{
    public function index(): Response
    {
        $accountReports = ReportsCollection::register()
            ->where('title', 'Account Reports')
            ->initializeAll(
                Auth::user()->currentTeam
            )
            ->first();

        return Inertia::render('Admin/Teams/AccountReports', compact('accountReports'));
    }
}
