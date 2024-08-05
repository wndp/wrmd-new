<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;

class ReleaseRate extends Chart
{
    use HandlePieData;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->handlePieData();
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw("sum(if(`disposition` = 'Released', 1, 0)) as `yes`")
            ->selectRaw("sum(if(`disposition` like '%Died%' or `disposition` like '%Euthanized%' or `disposition` = 'Pending', 1, 0)) as `no`")
            ->joinPatients();

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        $this->withSegment($query, $segment);

        return $this->aggregateData($query->get());
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw("sum(if(`disposition` = 'Released', 1, 0)) as `yes`")
            ->selectRaw("sum(if(`disposition` like '%Died%' or `disposition` like '%Euthanized%' or `disposition` = 'Pending', 1, 0)) as `no`")
            ->joinPatients()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to);

        $this->withSegment($query, $segment);

        return $this->aggregateData($query->get());
    }

    public function aggregateData($data)
    {
        return $data->flatMap(function ($data) {
            return [
                ['subgroup' => 'Released', 'aggregate' => intval($data['yes'])],
                ['subgroup' => 'Not Released', 'aggregate' => intval($data['no'])],
            ];
        });
    }
}
