<?php

namespace Database\Factories;

use App\Models\AttributeOption;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam>
 */
class ExamFactory extends Factory
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
            'date_examined_at' => $this->faker->date(),
            'exam_type_id' => AttributeOption::factory(),
            'sex_id' => AttributeOption::factory(),
            'weight' => $this->faker->randomFloat(2, 10, 500),
            'weight_unit_id' => AttributeOption::factory(),
            'body_condition_id' => AttributeOption::factory(),
            'age' => null,
            'age_unit_id' => AttributeOption::factory(),
            'attitude_id' => AttributeOption::factory(),
            'dehydration_id' => AttributeOption::factory(),
            'temperature' => '',
            'temperature_unit_id' => AttributeOption::factory(),
            'mucous_membrane_color_id' => AttributeOption::factory(),
            'head' => $this->faker->sentence(),
            'cns' => $this->faker->sentence(),
            'cardiopulmonary' => $this->faker->sentence(),
            'gastrointestinal' => $this->faker->sentence(),
            'musculoskeletal' => $this->faker->sentence(),
            'integument' => $this->faker->sentence(),
            'forelimb' => $this->faker->sentence(),
            'hindlimb' => $this->faker->sentence(),
            'treatment' => $this->faker->sentence(),
            'nutrition' => $this->faker->sentence(),
            'comments' => $this->faker->sentence(),
            'examiner' => $this->faker->words(2, true),
        ];
    }
}
