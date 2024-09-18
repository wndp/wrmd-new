<?php

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'user_id' => User::factory(),
            'personal_team' => true,
            'status' => AccountStatus::ACTIVE,
            'contact_name' => $this->faker->name,
            'contact_email' => $this->faker->email,
            'country' => 'US',
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'subdivision' => $this->faker->stateAbbr(),
            'postal_code' => $this->faker->postcode,
            'phone_number' => $this->faker->phoneNumber,
        ];
    }
}
