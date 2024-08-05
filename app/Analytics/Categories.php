<?php

namespace App\Analytics;

use App\Analytics\Concerns\GroupsDataByTimePeriod;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class Categories extends Collection
{
    use GroupsDataByTimePeriod;

    public function forTimePeriod(CarbonPeriod $period, AnalyticFilters $filters, $attribute = 'date')
    {
        $period = (new static($period->toArray()))
            ->map(function ($date) use ($attribute) {
                return (object) [$attribute => $date];
            });

        $dateCategories = $period->groupByTimePeriod($filters->group_by_period)->keys();

        return new static($dateCategories);
    }

    public function forDates(AnalyticFilters $filters)
    {
        return new static(
            $this->groupByTimePeriod($filters->group_by_period)->keys()
        );
    }
}
