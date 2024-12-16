<?php

namespace Database\Seeders;

use App\Enums\Ability as AbilityEnum;
use App\Enums\Role as RoleEnum;
use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (RoleEnum::cases() as $value) {
            Role::firstOrCreate([
                'name' => $value->value,
                'title' => $value->label(),
            ]);
        }

        foreach (AbilityEnum::cases() as $value) {
            Ability::firstOrCreate([
                'name' => $value->value,
                'title' => $value->label(),
            ]);
        }

        BouncerFacade::allow(RoleEnum::WNDP_SUPER_ADMIN->value)->to(
            array_column(AbilityEnum::cases(), 'value')
        );

        BouncerFacade::allow(RoleEnum::ADMIN->value)->to(
            array_column(AbilityEnum::publicAbilities(), 'value')
        );

        // Fill this out later
        BouncerFacade::allow(RoleEnum::USER->value)->to([

        ]);

        // Fill this out later
        BouncerFacade::allow(RoleEnum::VIEWER->value)->to([

        ]);
    }
}
