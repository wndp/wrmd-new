<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Enums\Role;
use App\Enums\SettingKey;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperationsPolicy
{
    use HandlesAuthorization;

    public function viewBilling($user)
    {
        return $user->isA(Role::ADMIN->value);
    }

    public function viewSettings($user)
    {
        if ($user->currentTeam?->isSubAccount()) {
            if ($user->roleOn($user->currentTeam->masterAccount)->name === Role::ADMIN->value) {
                return true;
            } elseif ($user->currentTeam->settingsStore()->get(SettingKey::SUB_ACCOUNT_ALLOW_MANAGE_SETTINGS) === false) {
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
        if ($user->currentTeam?->settingsStore()->get(SettingKey::SUB_ACCOUNT_ALLOW_TRANSFER_PATIENTS) === false) {
            return false;
        }

        return $user->can(Ability::SHARE_PATIENTS->value);
    }

    public function viewSubAccounts($user)
    {
        return $user->currentTeam?->is_master_account && $user->isA(Role::ADMIN->value);
    }
}
