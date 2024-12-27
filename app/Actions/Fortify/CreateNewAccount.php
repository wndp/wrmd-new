<?php

namespace App\Actions\Fortify;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use App\Rules\CountryRule;
use App\Rules\SubdivisionRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Silber\Bouncer\BouncerFacade;

class CreateNewAccount implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'confirmed'],
            'password' => $this->passwordRules(),
            'organization' => ['required', 'string'],
            'country' => ['required', 'string', new CountryRule],
            'address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'subdivision' => ['required', 'string', new SubdivisionRule],
            'phone' => ['required', 'string', 'phone:country'],
            'timezone' => ['required', 'timezone'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) use ($input) {
                $this->createTeam($user, $input);
            });
        });
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user, array $input): void
    {
        $user->ownedTeams()->save($team = Team::forceCreate([
            'user_id' => $user->id,
            'name' => $input['organization'],
            'personal_team' => true,
            'status' => AccountStatus::ACTIVE->value,
            'contact_name' => $input['name'],
            'contact_email' => $input['email'],
            'country' => $input['country'],
            'address' => $input['address'],
            'city' => $input['city'],
            'subdivision' => $input['subdivision'],
            'phone' => $input['phone'],
            'timezone' => $input['timezone'],
        ]));

        $team->settingsStore()->set([
            'timezone' => $input['timezone'] ?: 'America/Los_Angeles',
            'language' => config('app.fallback_locale'),
        ]);

        BouncerFacade::scope()->to($team->id)->onlyRelations()->dontScopeRoleAbilities();
        BouncerFacade::assign(Role::ADMIN->value)->to($user);
    }
}
