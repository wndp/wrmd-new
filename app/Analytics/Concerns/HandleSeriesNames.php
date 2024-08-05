<?php

namespace App\Analytics\Concerns;

use App\Analytics\AnalyticFilters;
use Carbon\Carbon;

trait HandleSeriesNames
{
    /**
     * Name each series.
     *
     * @param  \App\Domain\Analytics\AnalyticFilters  $filters
     * @return static
     */
    // public function makeSeriesName($segment, $compare, $dateFrom, $dateTo)
    // {
    //     $this->dd();
    //     return new static(
    //         $this->map(function ($collection) use ($segment, $compare, $dateFrom, $dateTo) {
    //             return $collection->mapWithKeys(function ($data, $seriesName) use ($segment, $compare, $dateFrom, $dateTo) {
    //                 $name = $this->formatSeriesName($segment, $seriesName, $compare, $dateFrom, $dateTo);
    //                 return [$name => $data];
    //             });
    //         })
    //     );
    // }

    /**
     * Format a series' name.
     */
    public function formatSeriesName($segment, string $name, $compare, $dateFrom, $dateTo): string
    {
        if (is_numeric($name)) {
            $name = '';
        }

        if ($segment === $name) {
            $segment = '';
        }

        if (! is_null($segment) && $segment !== AnalyticFilters::defaultSegment()) {
            $name = trim("$segment: $name", ' :');
        }

        if ($compare) {
            $from = Carbon::parse($dateFrom)->format(config('wrmd.date_format'));
            $to = Carbon::parse($dateTo)->format(config('wrmd.date_format'));

            return trim("$name: $from - $to", ' :');
        }

        return trim($name, ' :');
    }
}
