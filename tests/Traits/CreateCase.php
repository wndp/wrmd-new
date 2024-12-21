<?php

namespace Tests\Traits;

use App\Concerns\PersistsAdmission;
use App\Models\Admission;
use App\Models\Patient;
use App\Models\Person;
use App\Models\Team;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

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

    // public function castToJson($json): Expression
    // {
    //     // Convert from array to json and add slashes, if necessary.
    //     if (is_array($json)) {
    //         $json = addslashes(json_encode($json));
    //     }
    //     // Or check if the value is malformed.
    //     elseif (is_null($json) || is_null(json_decode($json))) {
    //         throw new \Exception('A valid JSON string was not provided.');
    //     }

    //     return DB::raw("TO_JSON('{$json}')");
    // }
}
