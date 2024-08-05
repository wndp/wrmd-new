<?php

namespace App\Actions\Fortify;

use App\Enums\AccountStatus;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
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
            'country' => ['required', 'string'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'subdivision' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
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
        $user->ownedTeams()->save(Team::forceCreate([
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
            'phone_number' => $input['phone_number'],
            'timezone' => $input['timezone'],
        ]));
    }
}
