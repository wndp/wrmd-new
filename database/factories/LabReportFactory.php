<?php

namespace Database\Factories;

use App\Models\AttributeOption;
use App\Models\LabFecalResult;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LabReport>
 */
class LabReportFactory extends Factory
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
            'lab_result_id' => LabFecalResult::factory(),
            'lab_result_type' => fn (array $attributes) => LabFecalResult::find($attributes['lab_result_id'])->getMorphClass(),
            'analysis_facility' => $this->faker->word(),
            'analysis_date_at' => $this->faker->dateTimeBetween('1 days', '90 days')->format('Y-m-d'),
        ];
    }
}