<?php

namespace Database\Factories;

use App\Enums\AttributeOptionUiBehavior;
use App\Models\AttributeOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttributeOptionUiBehavior>
 */
class AttributeOptionUiBehaviorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attribute_option_id' => AttributeOption::factory(),
            'behavior' => AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING,
        ];
    }
}
