<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyTask>
 */
class DailyTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'occurrence' => 1,
            'occurrence_at' => $this->faker->dateTimeBetween('1 days', '90 days')->format('Y-m-d'),
            'summary' => $this->faker->sentence(),
        ];
    }
}
