<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class ExpenseTransactionCollection extends Collection
{
    /**
     * Calculate a sum of the transactions debits.
     */
    public function totalDebits(bool $formatted = false)
    {
        $sum = $this->sum('debit');

        return $formatted ? number_format($sum / 100, 2) : $sum;
    }

    /**
     * Calculate a sum of the transactions credits.
     */
    public function totalCredits(bool $formatted = false)
    {
        $sum = $this->sum('credit');

        return $formatted ? number_format($sum / 100, 2) : $sum;
    }

    /**
     * Calculate the cost of care.
     */
    public function costOfCare(bool $formatted = false)
    {
        $difference = $this->totalDebits() - $this->totalCredits();

        return $formatted ? number_format($difference / 100, 2) : $difference;
    }

    /**
     * Calculate the cost of care of a TransactionCollections child TransactionCollections.
     */
    public function sumOfTransactions(bool $formatted = false)
    {
        $difference = $this->sum->costOfCare();

        return $formatted ? number_format($difference / 100, 2) : $difference;
    }
}
