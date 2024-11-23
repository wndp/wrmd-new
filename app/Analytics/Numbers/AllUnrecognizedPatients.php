<?php

namespace App\Analytics\Numbers;

use App\Analytics\Contracts\Number;
use App\Enums\AccountStatus;
use App\Models\Admission;

class AllUnrecognizedPatients extends Number
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $now = $this->query();
        $compare = null;

        $this->calculatePercentageDifference($now, $compare);

        $this->now = number_format($now);
        $this->prev = null;
    }

    public function query()
    {
        $query = Admission::join('teams', 'admissions.team_id', '=', 'teams.id')
            ->where('teams.status', AccountStatus::ACTIVE)
            ->whereUnrecognized();

        return $query->count();
    }
}
