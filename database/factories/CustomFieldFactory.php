<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomField>
 */
class CustomFieldFactory extends Factory
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
            'team_field_id' => $this->faker->randomDigitNotZero(),
            'group' => $this->faker->word(),
            'location' => $this->faker->word(),
            'type' => $this->faker->word(),
            'label' => $this->faker->word(),
        ];
    }
}
