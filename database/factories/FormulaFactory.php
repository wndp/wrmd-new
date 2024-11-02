<?php

namespace Database\Factories;

use App\Enums\FormulaType;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formula>
 */
class FormulaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory()->createQuietly(),
            'name' => $this->faker->word(),
            'type' => FormulaType::PRESCRIPTION,
            'defaults' => []
        ];
    }
}
