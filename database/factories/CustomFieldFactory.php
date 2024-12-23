<?php

namespace Database\Factories;

use App\Models\AttributeOption;
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
            'team_id' => Team::factory()->createQuietly(),
            'team_field_id' => $this->faker->randomDigitNotZero(),
            'group_id' => AttributeOption::factory(),
            'location_id' => AttributeOption::factory(),
            'type_id' => AttributeOption::factory(),
            'label' => $this->faker->word(),
        ];
    }
}
