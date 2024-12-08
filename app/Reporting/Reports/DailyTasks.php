<?php

namespace App\Reporting\Reports;

use App\Collections\DailyTasksCollection;
use App\DailyTasks\DailyTasksFilters;
use App\Reporting\Contracts\Report;

class DailyTasks extends Report
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.daily-tasks';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Daily Tasks');
    }

    public function data(): array
    {
        $data = DailyTasksCollection::make()
            ->withFilters(new DailyTasksFilters($this->request->toArray()))
            ->forAccount($this->team);

        return compact('data');
    }
}
