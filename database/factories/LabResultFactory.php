<?php

namespace Database\Factories;

use App\Models\LabReport;
use App\Models\LabResultTemplateParameter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LabResult>
 */
class LabResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lab_report_id' => LabReport::factory(),
            'lab_result_template_parameter_id' => LabResultTemplateParameter::factory(),
            'value' => $this->faker->word()
        ];
    }
}
