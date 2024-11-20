<?php

namespace App\Analytics\Numbers;

use App\Analytics\Contracts\Number;
use App\Enums\AccountStatus;
use App\Models\Admission;

class AllPatientsAdmitted extends Number
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $segment = $this->filters->segments[0];

        $now = $this->query($segment);
        $compare = $this->filters->compare ? $this->compareQuery($segment) : null;

        $this->calculatePercentageDifference($now, $compare);

        $this->now = number_format($now);
        $this->prev = is_null($compare) ? null : number_format($compare);
    }

    public function query($segment)
    {
        $query = Admission::joinPatients()
            ->join('teams', 'admissions.team_id', '=', 'teams.id')
            ->where('teams.status', AccountStatus::ACTIVE);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        return $query->count();
    }
}
