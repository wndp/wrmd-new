<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Accounts\Account;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AccountSpoofController extends Controller
{
    /**
     * Spoof an account
     */
    public function __invoke(Account $account): RedirectResponse
    {
        Auth::user()->joinAccount($account);
        Auth::user()->switchToAccount($account);

        session()->put('isSpoofing', true);

        return redirect()->route('dashboard')
            ->with('flash.notificationHeading', 'Signed In!')
            ->with('flash.notification', "You are now signed into $account->organization.");
    }
}
