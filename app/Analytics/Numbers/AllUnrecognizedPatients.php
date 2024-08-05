<?php

namespace App\Analytics\Numbers;

use App\Models\Admission;
use App\Analytics\Contracts\Number;

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
        $query = Admission::join('accounts', 'admissions.team_id', '=', 'accounts.id')
            ->where('disposition', '!=', 'Void')
            ->where('accounts.is_active', true)
            ->whereUnrecognized();

        return $query->count();
    }
}
