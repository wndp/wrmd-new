<?php

namespace App\Analytics\Numbers;

use App\Domain\Accounts\Account;
use App\Analytics\Contracts\Number;

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
        return Account::distinct('subdivision')->where('status', 'Active')->where('country', 'US')->count();
    }
}
