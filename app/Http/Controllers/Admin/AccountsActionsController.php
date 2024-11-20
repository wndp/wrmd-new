<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Accounts\Account;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AccountsActionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Account $account): Response
    {
        return Inertia::render('Admin/Accounts/Actions', compact('account'));
    }
}
