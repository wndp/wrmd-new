<?php

namespace App\Reporting\Reports\Expenses;

use App\Models\ExpenseTransaction;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ExpensesByCategoryDetail extends Report
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.expenses.category-detail';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Patient Expenses By Category Detail');
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect((new DateRange('transacted_at'))->toArray());
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        $query = ExpenseTransaction::select('expense_transactions.*')
            ->join('admissions', 'expense_transactions.patient_id', '=', 'admissions.patient_id')
            ->where('admissions.team_id', $this->team->id)
            ->with('patient.admissions');

        $parentCategories = $this->applyFilters($query)->get()
            ->groupBy([function ($transaction) {
                return $transaction->category->isParent() ? $transaction->category->name : $transaction->category->parent->name;
            }, function ($transaction) {
                return ($transaction->category->parent ? $transaction->category->parent->name.'::' : '').$transaction->category->name;
            }])
            ->sortBy(function ($collection, $key) {
                return $key;
            });

        $dateFrom = Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format(config('wrmd.date_format'));
        $dateTo = Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format(config('wrmd.date_format'));

        return compact('parentCategories', 'dateFrom', 'dateTo');
    }
}
