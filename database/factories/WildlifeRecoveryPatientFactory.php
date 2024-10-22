<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WildlifeRecoveryPatient>
 */
class WildlifeRecoveryPatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            //'patient_id' => Patient::factory()
        ];
    }
}
