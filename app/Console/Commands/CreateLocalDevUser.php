<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Enums\SettingKey;
use App\Models\User;
use Illuminate\Console\Command;
use Silber\Bouncer\BouncerFacade;

class CreateLocalDevUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wrmd:create-local-dev-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user for use in local development.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        BouncerFacade::scope()->to($user->personalTeam()->id)->onlyRelations()->dontScopeRoleAbilities();
        BouncerFacade::assign(Role::WNDP_SUPER_ADMIN->value)->to($user);

        $user->personalTeam()->settingsStore()->set([
            SettingKey::TIMEZONE->value => 'America/Los_Angeles',
            SettingKey::LANGUAGE->value => 'en',
        ]);
    }
}
