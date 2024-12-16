<?php

namespace Database\Factories;

use App\Models\NutritionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NutritionIngredient>
 */
class NutritionPlanIngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nutrition_plan_id' => NutritionPlan::factory(),
            'quantity' => $this->faker->randomDigitNotZero(),
            'ingredient' => $this->faker->words(3, true),
        ];
    }
}
