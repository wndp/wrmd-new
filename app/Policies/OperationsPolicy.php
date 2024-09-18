<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Enums\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperationsPolicy
{
    use HandlesAuthorization;

    public function viewSettings($user)
    {
        return true;

        if ($user->currentTeam?->isSubAccount()) {
            if ($user->roleOn($user->currentTeam->masterAccount)->name === Role::ADMIN->value) {
                return true;
            } elseif ($user->currentTeam->settingsStore()->get('subAccountAllowManageSettings') === false) {
                return false;
            }
        }

        return $user->isA(Role::ADMIN->value);
    }

    public function viewMaintenance($user)
    {
        return $user->isA(Role::ADMIN->value);
    }

    public function viewTransferPatient($user)
    {
        if ($user->currentTeam?->settingsStore()->get('subAccountAllowTransferPatients') === false) {
            return false;
        }

        return $user->can(Ability::SHARE_PATIENTS->value);
    }

    public function viewSubAccounts($user)
    {
        if (is_null($user->current_account_id)) {
            return false;
        }

        return $user->currentTeam?->is_master_account && $user->isA(Role::ADMIN->value);
    }
}
