<?php

namespace App\Reporting\Contracts;

use App\Domain\Admissions\Admission;
use App\Reporting\Filters\YearFilter;
use Illuminate\Support\Collection;

abstract class AnnualReport extends ExportableReport
{
    public $canExport = false;

    protected $year;

    /**
     * Get the reports filters.
     */
    public function filters(): Collection
    {
        return new Collection([
            new YearFilter(Admission::yearsInTeam($this->team->id)),
        ]);
    }

    /**
     * [data description].
     *
     * @return [type] [description]
     */
    public function data(): array
    {
        $this->year = $this->getAppliedFilterValue(YearFilter::class);

        return [];
    }
}
