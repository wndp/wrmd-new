<?php

namespace Database\Factories;

use App\Models\AttributeOption;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
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
            'facility_id' => AttributeOption::factory(),
            'area' => $this->faker->word(),
        ];
    }
}
