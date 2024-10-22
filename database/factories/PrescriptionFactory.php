<?php

namespace Database\Factories;

use App\Models\AttributeOption;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prescription>
 */
class PrescriptionFactory extends Factory
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
            'drug' => $this->faker->words(3, true),
            'concentration' => $this->faker->randomFloat(),
            'concentration_unit_id' => AttributeOption::factory(),
            'dosage' => $this->faker->randomFloat(),
            'dosage_unit_id' => AttributeOption::factory(),
            'loading_dose' => $this->faker->randomFloat(),
            'loading_dose_unit_id' => AttributeOption::factory(),
            'dose' => $this->faker->randomFloat(),
            'dose_unit_id' => AttributeOption::factory(),
            'frequency_id' => AttributeOption::factory(),
            'route_id' => AttributeOption::factory(),
            'rx_started_at' => $this->faker->dateTimeBetween('1 days', '90 days')->format('Y-m-d'),
            'rx_ended_at' => $this->faker->dateTimeBetween('1 days', '90 days')->format('Y-m-d'),
            'is_controlled_substance' => $this->faker->randomElement([0, 1]),
            'instructions' => $this->faker->sentence(),
        ];
    }
}
