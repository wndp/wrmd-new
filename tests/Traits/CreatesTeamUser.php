<?php

namespace Tests\Traits;

use App\Enums\Role;
use App\Enums\SettingKey;
use App\Models\Team;
use App\Models\User;
use App\Repositories\SettingsStore;
use Silber\Bouncer\BouncerFacade;

trait CreatesTeamUser
{
    public function createTeamUser(array $teamOverrides = [], array $userOverrides = [])
    {
        $role = isset($userOverrides['role']) ? $userOverrides['role'] : Role::ADMIN->value;
        unset($userOverrides['role']);

        if (isset($userOverrides['email'])) {
            $teamOverrides = array_merge($teamOverrides, ['contact_email' => $userOverrides['email']]);
        }

        $team = Team::factory()->create($teamOverrides);
        $team->settingsStore()->set(SettingKey::TIMEZONE, 'America/Los_Angeles');

        // This might be causing trouble
        //$this->scopeBouncerTo($team->id);

        //$userOverrides['parent_account_id'] = $team->id;
        $userOverrides['current_team_id'] = $team->id;

        $user = User::factory()->create($userOverrides);

        $team->users()->attach($user);
        BouncerFacade::scope()->to($team->id)->onlyRelations()->dontScopeRoleAbilities();
        BouncerFacade::assign($role)->to($user->id);

        //$user->joinAccount($team, $role);

        return (object) [
            'team' => $team,
            'user' => $user,
        ];
    }

    /**
     * Attach a new user to an existing account.
     */
    public function attachUser(Team $team, $userOverrides = [])
    {
        $role = isset($userOverrides['role']) ? $userOverrides['role'] : Role::ADMIN->value;
        unset($userOverrides['role']);

        //$userOverrides['parent_account_id'] = $team->id;
        $userOverrides['current_team_id'] = $team->id;

        $user = User::factory()->create($userOverrides);

        //$user->joinAccount($team, $role);
        $team->users()->attach($user);
        BouncerFacade::scope()->to($team->id)->onlyRelations()->dontScopeRoleAbilities();
        BouncerFacade::assign($role)->to($user->id);

        return $user;
    }

    public function scopeBouncerTo($teamId)
    {
        BouncerFacade::scope()->to($teamId)
            ->onlyRelations()
            ->dontScopeRoleAbilities();
    }

    public function setSetting(Team $team, SettingKey $key, $value = null)
    {
        app()->singleton(SettingsStore::class, fn () => $team->settingsStore());

        $team->settingsStore()->set($key, $value);
    }
}
