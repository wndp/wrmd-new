<?php

namespace Database\Factories;

use App\Models\AttributeOption;
use App\Models\Person;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
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
            'responder_id' => function (array $attributes) {
                return Person::factory()->create(['team_id' => $attributes['team_id']]);
            },
            'reported_at' => $this->faker->dateTimeBetween('-10 days', '10 days')->format('Y-m-d'),
            'occurred_at' => $this->faker->dateTimeBetween('-10 days', '10 days')->format('Y-m-d'),
            'recorded_by' => $this->faker->name,
            'incident_status_id' => AttributeOption::factory(),
        ];
    }
}
