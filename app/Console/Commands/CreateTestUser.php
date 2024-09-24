<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Paddle\Cashier;
use Silber\Bouncer\BouncerFacade;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        BouncerFacade::assign(Role::ADMIN->value)->to($user);

        $user->personalTeam()->settingsStore()->set([
            'timezone' => 'America/Los_Angeles'
        ]);
    }
}
