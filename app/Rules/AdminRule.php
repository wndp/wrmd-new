<?php

namespace App\Rules;

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AdminRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected User $user, protected Team $team)
    {
        $this->user = $user;
        $this->team = $team;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail('Accounts are required to have at least 1 Admin.');
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  mixed  $value
     */
    public function passes(string $attribute, string $newRole): bool
    {
        $doesAdminExist = false;
        //$newRole = Str::kebab($newRole);

        if ($newRole === Role::ADMIN->value) {
            return true;
        }

        // ->fresh()
        foreach ($this->user->teammatesOnTeam($this->team) as $teammate) {
            if ($teammate->isA(Role::ADMIN->value)) {
                $doesAdminExist = true;

                if ($teammate->id == $this->user->id && $newRole !== Role::ADMIN->value) {
                    $doesAdminExist = false;
                } else {
                    $doesAdminExist = true;
                    break;
                }
            }
        }

        if (! $doesAdminExist) {
            return false;
        }

        return true;
    }
}
