<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Accounts\Account;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AccountsUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Account $account)
    {
        $users = $account->users->append([
            'created_at_diff_for_humans',
        ])
            ->transform(function ($user) use ($account) {
                $user->role_name_for_humans = $user->getRoleNameOnAccountForHumans($account);

                return $user;
            });

        return Inertia::render('Admin/Accounts/Users', compact('account', 'users'));
    }
}
