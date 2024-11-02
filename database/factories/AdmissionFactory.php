<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admission>
 */
class AdmissionFactory extends Factory
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
            'case_year' => null,
            'case_id' => 1,
            'patient_id' => Patient::factory(),
            'hash' => null
        ];
    }
}
