<?php

namespace App\Reporting\Reports\Expenses;

use App\Models\Admission;
use App\Reporting\Contracts\Report;

class ExpenseStatement extends Report
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.expenses.separate-statement';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Expense Statement');
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        return [
            'admission' => Admission::custody($this->team, $this->patient),
            'transactions' => $this->patient->expenses,
        ];
    }
}
