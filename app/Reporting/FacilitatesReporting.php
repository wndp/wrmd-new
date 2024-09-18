<?php

namespace App\Reporting;

use App\Reporting\Contracts\Report;
use Illuminate\Support\Arr;

trait FacilitatesReporting
{
    public function reportFromKey($key)
    {
        $guessedReport = null;

        ReportsCollection::register()->each(function ($reportGroup) use ($key, &$guessedReport) {
            return array_filter(Arr::flatten($reportGroup->reports), function ($report) use ($key, &$guessedReport) {
                if (is_subclass_of($report, Report::class) && $report::staticKey() === mb_strtolower($key)) {
                    $guessedReport = $report;
                }
            });
        });

        return $guessedReport;
    }
}
