<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseTransaction>
 */
class ExpenseTransactionFactory extends Factory
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
            'expense_category_id' => ExpenseCategory::factory(),
            'transacted_at' => $this->faker->dateTimeBetween('1 days', '90 days')->format('Y-m-d'),
        ];
    }
}
