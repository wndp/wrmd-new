<?php

namespace Database\Factories;

use App\Enums\LabReportType;
use App\Models\LabResultTemplate;
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
            'lab_result_template_id' => LabResultTemplate::factory(),
            'type' => LabReportType::BIOTOXIN,
            'analysis_date_at' => $this->faker->dateTimeBetween('1 days', '90 days')->format('Y-m-d'),
        ];
    }
}
