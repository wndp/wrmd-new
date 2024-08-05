<?php

namespace App\Analytics\Charts;

use App\Analytics\Contracts\Chart;
use App\Domain\Hotline\Models\Incident;

class IncidentsByTimeOfDayReported extends Chart
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $data = Incident::where('team_id', $this->team->id)
            ->selectRaw('dayofweek(reported_at) as x, hour(reported_at)  as y')
            ->orderBy('reported_at')
            ->get();

        $this->categories = [1 => 'Sunday', 2 => 'Monday', 3 => 'Tuesday', 4 => 'Wednesday', 5 => 'Thursday', 6 => 'Friday', 7 => 'Saturday'];
        $this->series = $this->setScatterSeries($data);
        $this->yAxisTitle = 'Hour of the Day';
    }

    public function setScatterSeries($collection)
    {
        $y = $collection->pluck('y')->map(function ($y) {
            return floatval($y);
        })->all();

        return [[
            'name' => 'Female',
            'data' => $collection->pluck('x')->zip($y),
        ]];
    }
}
