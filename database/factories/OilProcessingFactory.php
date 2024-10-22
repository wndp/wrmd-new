<?php

namespace Database\Factories;

use App\Models\AttributeOption;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OilConditioning>
 */
class OilProcessingFactory extends Factory
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
            'date_collected_at' => $this->faker->dateTimeBetween('1 days', '90 days')->format('Y-m-d'),
            'collection_condition_id' => AttributeOption::factory()
        ];
    }
}
