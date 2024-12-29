<?php

namespace Database\Factories;

use App\Models\AttributeOption;
use App\Models\Person;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_possession_id' => Team::factory()->createQuietly(),
            'rescuer_id' => Person::factory(),
            'taxon_id' => null,
            'is_resident' => false,
            'locked_at' => null,
            'voided_at' => null,
            'common_name' => 'Example Critter',
            'date_admitted_at' => $this->faker->date(),
            'time_admitted_at' => $this->faker->time(),
            'admitted_by' => $this->faker->firstName(),
            'found_at' => $this->faker->date(),
            'address_found' => $this->faker->streetAddress(),
            'city_found' => $this->faker->city(),
            'county_found' => $this->faker->word(),
            'subdivision_found' => $this->faker->stateAbbr(),
            'reason_for_admission' => $this->faker->sentence(),
            'care_by_rescuer' => $this->faker->sentence(),
            'diagnosis' => $this->faker->sentence(),
            'band' => $this->faker->word(),
            'name' => $this->faker->word(),
            //'keywords' => $this->faker->word(),
            'disposition_id' => AttributeOption::factory(),
            'transfer_type_id' => AttributeOption::factory(),
            'release_type_id' => AttributeOption::factory(),
            'dispositioned_at' => null,
            'disposition_address' => null,
            'disposition_subdivision' => null,
            'disposition_county' => null,
            'is_carcass_saved' => $this->faker->randomElement([0, 1]),
            'is_criminal_activity' => $this->faker->randomElement([0, 1]),
        ];
    }

    public function voided()
    {
        return $this->state(function (array $attributes) {
            return [
                'voided_at' => Carbon::now(),
            ];
        });
    }
}
