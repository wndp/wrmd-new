<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\BouncerFacade;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->cleanMedia();
        $this->cleanDatabase();

        $this->call([
            AttributeOptionSeeder::class,
            PermissionsSeeder::class,
        ]);

        $user = User::factory()->withPersonalTeam()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        BouncerFacade::scope()->to($user->personalTeam()->id)->onlyRelations()->dontScopeRoleAbilities();
        BouncerFacade::assign(Role::ADMIN->value)->to($user);

        $user->personalTeam()->settingsStore()->set([
            'timezone' => 'America/Los_Angeles'
        ]);

        Cache::clear();
    }

    private function cleanMedia()
    {
        if (App::isProduction()) {
            return;
        }

        Media::get()->each->delete();
    }

    private function cleanDatabase()
    {
        if (App::isProduction()) {
            return;
        }

        DB::table('information_schema.tables')
            ->where('table_schema', app('db')->getDatabaseName())
            ->whereNotIn('table_name', [
                'migrations'
            ])
            ->pluck('TABLE_NAME')
            ->each(fn ($table) => DB::table($table)->truncate());
    }
}
