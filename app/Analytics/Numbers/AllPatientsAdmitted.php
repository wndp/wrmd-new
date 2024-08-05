<?php

namespace App\Analytics\Numbers;

use App\Models\Admission;
use App\Analytics\Contracts\Number;

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
            ->join('accounts', 'admissions.team_id', '=', 'accounts.id')
            ->where('accounts.is_active', true);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        return $query->count();
    }
}
