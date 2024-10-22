<?php

namespace Database\Factories;

use App\Enums\DataType;
use App\Models\AttributeOption;
use App\Models\LabResultTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LabResultTemplateParameters>
 */
class LabResultTemplateParameterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lab_result_template_id' => LabResultTemplate::factory(),
            'parameter_id' => AttributeOption::factory(),
            'data_type' => DataType::STRING,
            'sort_order' => 1
        ];
    }
}
