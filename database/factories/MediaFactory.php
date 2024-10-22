<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_type' => Patient::class,
            'model_id' => $this->faker->uuid(),
            'collection_name' => 'default',
            'name' => $this->faker->word(),
            'file_name' => $this->faker->word(),
            'disk' => $this->faker->word(),
            'size' => $this->faker->randomDigitNotNull(),
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
        ];
    }
}
