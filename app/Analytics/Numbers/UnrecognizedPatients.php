<?php

namespace App\Analytics\Numbers;

use App\Models\Admission;
use App\Analytics\Contracts\Number;

class UnrecognizedPatients extends Number
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
        $query = Admission::where('team_id', $this->team->id)
            ->where('disposition', '!=', 'Void')
            ->whereUnrecognized();

        return $query->count();
    }
}
