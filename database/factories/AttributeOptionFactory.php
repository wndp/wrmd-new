<?php

namespace Database\Factories;

use App\Enums\AttributeOptionName;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttributeOption>
 */
class AttributeOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => AttributeOptionName::PATIENT_DISPOSITIONS,
            'value' => $this->faker->word(),
            'value_lowercase' => $this->faker->word(),
            'sort_order' => null,
        ];
    }
}
