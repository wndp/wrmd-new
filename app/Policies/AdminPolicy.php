<?php

namespace App\Policies;

use App\Enums\WrmdStaff;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    public function viewAdmin($user)
    {
        return in_array($user->id, [
            WrmdStaff::AMY->value,
            WrmdStaff::TERRA->value,
            WrmdStaff::PRANAV->value,
        ]);
    }

    public function manageMaintenance($user)
    {
        return in_array($user->id, []);
    }

    public function manageAccounts($user)
    {
        return in_array($user->id, []);
    }

    public function spoofAccounts($user)
    {
        return in_array($user->id, []);
    }

    public function manageAuthorizations($user)
    {
        return in_array($user->id, []);
    }

    public function viewRevisions($user)
    {
        return in_array($user->id, []);
    }

    public function importPatients($user)
    {
        return in_array($user->id, []);
    }
}
