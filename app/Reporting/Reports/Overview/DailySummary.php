<?php

namespace App\Reporting\Reports\Overview;

use App\Models\Admission;
use App\Models\Donation;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateOn;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DailySummary extends Report
{
    /**
     * {@inheritdoc}
     */
    public function title(): string
    {
        return __('Daily Summary Report');
    }

    /**
     * Get the reports explanation.
     */
    public function explanation(): string
    {
        return 'The Daily Summary Report was designed to give you a snapshot of what happened on a given day. It shows what was admitted, dispositioned and who was in care on that day.';
    }

    /**
     * {@inheritdoc}
     */
    public function viewPath(): string
    {
        return 'reports.overview.daily-summary';
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect([
            new DateOn,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function data(): array
    {
        $this->date = Carbon::parse($this->getAppliedFilterValue(DateOn::class));

        return [
            'date' => $this->date,
            'admitted' => Admission::where('team_id', $this->team->id)
                ->select('admissions.*')
                ->joinPatients()
                ->whereDate('date_admitted_at', $this->date->format('Y-m-d'))
                ->get(),

            'dispositioned' => Admission::where('team_id', $this->team->id)
                ->select('admissions.*')
                ->joinPatients()
                ->whereDate('dispositioned_at', $this->date->format('Y-m-d'))
                ->get(),

            'inCare' => Admission::inCareOnDate($this->team, $this->date),

            'donations' => Donation::select('donations.*')
                ->join('people', 'donations.person_id', '=', 'people.id')
                ->where('team_id', $this->team->id)
                ->whereDate('donated_at', $this->date->format('Y-m-d'))
                ->with('person')
                ->get(),
        ];
    }
}
