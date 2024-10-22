<?php

namespace Database\Factories;

use App\Models\AttributeOption;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recheck>
 */
class RecheckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'recheck_start_at' => $this->faker->dateTimeBetween('-10 days', '10 days')->format('Y-m-d'),
            'recheck_end_at' => $this->faker->dateTimeBetween('-10 days', '10 days')->format('Y-m-d'),
            'assigned_to_id' => AttributeOption::factory(),
            'description' => $this->faker->sentence(),
        ];
    }
}
