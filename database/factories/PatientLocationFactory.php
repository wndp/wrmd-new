<?php

namespace Database\Factories;

use App\Models\AttributeOption;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientLocation>
 */
class PatientLocationFactory extends Factory
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
            'facility_id' => AttributeOption::factory(),
            'area' => $this->faker->word(),
        ];
    }
}
