<?php

namespace App\Analytics\Numbers;

use App\Analytics\Contracts\Number;
use App\Enums\AccountStatus;
use App\Models\Team;

class ActiveUsStates extends Number
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
        return Team::distinct('subdivision')->where('status', AccountStatus::ACTIVE)->where('country', 'US')->count();
    }
}
