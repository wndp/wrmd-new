<?php

namespace App\Analytics\Numbers;

use App\Analytics\Contracts\Number;
use App\Models\Admission;

class PatientsAdmitted extends Number
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
        $query = Admission::where('team_id', $this->team->id)->joinPatients();

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->count();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->joinPatients()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at');

        $this->withSegment($query, $segment);

        return $query->count();
    }
}
