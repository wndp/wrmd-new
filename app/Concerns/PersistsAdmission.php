<?php

namespace App\Concerns;

use App\Models\Admission;
use App\Models\Patient;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

trait PersistsAdmission
{
    /**
     * Persist the admission to storage.
     *
     * @link https://mysql.rjweb.org/doc.php/myisam2innodb
     *
     * @note INDEX issue -- 2-column PK
     */
    public function persistAdmission(Team $team, Patient $patient, $year)
    {
        return DB::transaction(function () use ($team, $patient, $year) {
            $nextCaseId = Admission::where([
                'team_id' => $team->id,
                'case_year' => $year,
            ])
                ->lockForUpdate()
                ->count() + 1;

            return Admission::create([
                'team_id' => $team->id,
                'case_year' => $year,
                'case_id' => $nextCaseId,
                'patient_id' => $patient->id,
            ]);
        }, 5);
    }
}
