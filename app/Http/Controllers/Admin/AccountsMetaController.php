<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Accounts\Account;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AccountsMetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Account $account): Response
    {
        $account->load('settings', 'customFields');

        return Inertia::render('Admin/Accounts/Meta', compact('account'));
    }
}
