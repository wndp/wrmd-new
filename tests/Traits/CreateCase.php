<?php

namespace Tests\Traits;

use App\Concerns\PersistsAdmission;
use App\Models\Admission;
use App\Models\Patient;
use App\Models\Person;
use App\Models\Team;

trait CreateCase
{
    use PersistsAdmission;

    public function createCase(
        ?Team $team = null,
        ?int $caseYear = null,
        array $patientOverrides = [],
        array $rescuerOverrides = []
    ): Admission {
        if (! $team instanceof Team) {
            $team = Team::factory()->create();
        }

        $rescuerOverrides['team_id'] = $team->id;
        $patientOverrides['team_possession_id'] = $team->id;

        if (! array_key_exists('rescuer_id', $patientOverrides)) {
            $rescuer = Person::factory()->create($rescuerOverrides);
            $patientOverrides = array_merge($patientOverrides, [
                'rescuer_id' => $rescuer->id,
            ]);
        }

        return $this->persistAdmission(
            $team,
            Patient::factory()->create($patientOverrides),
            is_int($caseYear) ? $caseYear : (int) now()->format('Y')
        );
    }
}
