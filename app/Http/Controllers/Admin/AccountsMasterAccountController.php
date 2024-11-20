<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Accounts\Account;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountsMasterAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Account $account, Request $request)
    {
        $account->update([
            'master_account_id' => $request->master_account_id
        ]);

        return back();
    }
}

