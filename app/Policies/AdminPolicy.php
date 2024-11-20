<?php

namespace App\Policies;

use App\Enums\WrmdStaff;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    public function viewWrmdAdmin($user)
    {
        $userIds = array_column(WrmdStaff::cases(), 'value');

        return in_array($user->id, $userIds);
    }

    public function manageMaintenance($user)
    {
        return in_array($user->id, [
            WrmdStaff::DEVIN->value,
            WrmdStaff::RACHEL->value,
            WrmdStaff::MISTY->value,
        ]);
    }

    public function manageAccounts($user)
    {
        return in_array($user->id, [
            WrmdStaff::DEVIN->value,
            WrmdStaff::RACHEL->value,
            WrmdStaff::MISTY->value,
            WrmdStaff::BRITTANY->value,
        ]);
    }

    public function spoofAccounts($user)
    {
        return in_array($user->id, [
            WrmdStaff::DEVIN->value,
            WrmdStaff::RACHEL->value,
            WrmdStaff::MISTY->value,
        ]);
    }

    public function manageAuthorizations($user)
    {
        return in_array($user->id, [
            WrmdStaff::DEVIN->value,
            WrmdStaff::RACHEL->value,
        ]);
    }

    public function viewRevisions($user)
    {
        return in_array($user->id, [
            WrmdStaff::DEVIN->value,
            WrmdStaff::RACHEL->value,
            WrmdStaff::MISTY->value,
        ]);
    }

    public function importPatients($user)
    {
        return in_array($user->id, [
            WrmdStaff::DEVIN->value,
            WrmdStaff::RACHEL->value,
            WrmdStaff::MISTY->value,
        ]);
    }
}
