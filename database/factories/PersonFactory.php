<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'organization' => $this->faker->company(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->firstName(),
            'phone' => $this->faker->phoneNumber(),
            'alternate_phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'country' => $this->faker->randomElement(['US', 'US', $this->faker->countryCode()]),
            'subdivision' => $this->faker->stateAbbr(),
            'city' => $this->faker->city(),
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
        ];
    }
}
