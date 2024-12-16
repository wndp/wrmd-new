<?php

namespace App\Analytics\Charts;

use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChronologicalData;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Enums\AccountStatus;
use App\Models\Admission;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AllPatientsAdmitted extends Chart
{
    use HandleChronologicalData;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->handleChronologicalData(null, Carbon::parse('1980-01-01'));
    }

    /**
     * Query for the requested data.
     */
    public function query(string $segment, ?CarbonPeriod $period = null): ChronologicalCollection
    {
        $query = Admission::joinPatients()
            ->join('teams', 'admissions.team_id', '=', 'teams.id')
            ->selectRaw('count(*) as aggregate, date_admitted_at as date')
            ->where('teams.status', AccountStatus::ACTIVE)
            ->groupBy('date')
            ->orderBy('date');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        } else {
            $query->dateRange($period->first()->format('Y-m-d'), $period->last()->format('Y-m-d'), 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }

    /*
     * Query for the requested comparative data.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    // public function compareQuery($segment)
    // {
    //     $query = Admission::joinPatients()
    //         ->join('accounts', 'admissions.team_id', '=', 'accounts.id')
    //         ->selectRaw("count(*) as aggregate, date_admitted_at as date")
    //         ->where('accounts.is_active', true)
    //         ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
    //         ->groupBy('date')
    //         ->orderBy('date');

    //     $this->withSegment($query, $segment);

    //     return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    // }
}
