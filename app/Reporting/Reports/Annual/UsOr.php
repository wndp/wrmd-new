<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\Filters\HalfYear;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Oregon, United Sates
 */
class UsOr extends AnnualReport
{
    private $half = 1;

    /**
     * Get the pdf format options.
     */
    public function options(): array
    {
        return [
            'viewportSize' => 'auto',
            'viewportMargin' => '0 20px',
            'orientation' => 'landscape',
            'user-style-sheet' => public_path('css/report-landscape.css'),
        ];
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.annual.usOr';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Oregon Wildlife Rehabilitation Bi-Annual Report';
    }

    /**
     * Get the reports filters.
     */
    public function filters(): Collection
    {
        return parent::filters()->push(
            new HalfYear
        );
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();
        $this->half = intval($this->getAppliedFilterValue(HalfYear::class));

        $range = $this->half === 1
            ? [$this->year.'-01-01', $this->year.'-06-30']
            : [$this->year.'-07-01', $this->year.'-12-31'];

        $newWildlife = $this->newWildlife($range);

        $transfers = $newWildlife->filter(function ($case) {
            return $case->patient->disposition === 'Transferred';
        });

        $prohibited = $newWildlife->filter(function ($case) {
            return ! is_array($case->patient->taxon->native_distributions) || ! in_array('US-OR', $case->patient->taxon->native_distributions);
        });

        return [
            'year' => $this->year,
            'reportEnding' => Carbon::parse($range[1])->format(config('wrmd.date_format')),
            'newWildlife' => $newWildlife,
            'oldWildlife' => $this->oldWildlife($range),
            'transfers' => $transfers,
            'prohibited' => $prohibited,
        ];
    }

    /**
     * Get the new wildlife.
     */
    private function newWildlife($range): Collection
    {
        return Admission::owner($this->team->id, $this->year)
            ->select('admissions.*')
            ->joinPatients()
            ->where('disposition', '!=', 'Void')
            ->dateRange($range[0], $range[1])
            ->with('patient.taxon')
            ->get();
    }

    /**
     * Get the old wildlife.
     */
    private function oldWildlife($dispositionRange): Collection
    {
        // '2000-01-01' is just an arbitrary date far in the past to make sure that all
        // "old" un-dispositioned patients are picked up.
        $range = $this->half === 1
            ? ['2000-01-01', ($this->year - 1).'-12-31'] // ($this->year - 1) . '-07-01'
            : ['2000-01-01', $this->year.'-06-30']; // $this->year . '-01-01'

        return Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->where('disposition', '!=', 'Void')
            ->dateRange($range[0], $range[1])
            ->where(function ($q) use ($dispositionRange) {
                $q->where('disposition', 'Pending')
                    ->orWhere(function ($q) use ($dispositionRange) {
                        $q->dateRange($dispositionRange[0], $dispositionRange[1], 'dispositioned_at');
                    });
            })
            ->with('patient.taxon')
            ->get();
    }
}
