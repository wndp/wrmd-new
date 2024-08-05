<?php

namespace App\Analytics\Concerns;

use App\Models\Admission;
use App\Analytics\AnalyticFilters;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

trait HandleChartPeriod
{
    /**
     * Compute the chronological date period from the provided filters.
     */
    public function chartPeriod(AnalyticFilters $filters, Carbon $start = null): CarbonPeriod
    {
        if ($filters->date_period === 'all-dates') {
            $start = $start ?: Admission::where('team_id', $this->team->id)
                ->joinPatients()
                ->orderBy('date_admitted_at')
                ->limit(1)
                ->first()
                ->date_admitted_at;

            return new CarbonPeriod(
                Carbon::parse($start)->firstOfYear(),
                Carbon::now()
            );
        }

        $days = Carbon::parse($filters->date_from)->diffInDays(Carbon::parse("$filters->date_to 23:59:59"));

        $days = $filters->compare ? max(
            $days,
            Carbon::parse($filters->compare_date_from)->diffInDays(Carbon::parse($filters->compare_date_to))
        ) : $days;

        return new CarbonPeriod(
            Carbon::parse($filters->date_from),
            Carbon::parse($filters->date_from)->addDays($days)
        );
    }
}
