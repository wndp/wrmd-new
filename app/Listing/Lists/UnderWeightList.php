<?php

namespace App\Listing\Lists;

use App\Listing\ListingQuery;
use App\Listing\LiveList;
use Illuminate\Support\Collection;

class UnderWeightList extends LiveList
{
    /**
     * The lists route.
     *
     * @var string
     */
    public static $route = 'under-weight';

    /**
     * The lists title to display on the view.
     *
     * @var string
     */
    public function title(): string
    {
        return __('Patients With Significant Weight Loss');
    }

    /**
     * The lists icon to display on the view.
     *
     * @var string
     */
    public function icon(): string
    {
        return 'chart-pie';
    }

    /**
     * Return the cases to be displayed in the list.
     */
    public function data(): Collection
    {
        // $select = collect($this->columns)->diff(
        //     fields()->where('computed', true)->keys()->toArray()
        // );

        return ListingQuery::run()
            ->selectColumns($this->columns)
            ->selectAdmissionKeys()
            ->joinTables($this->columns)
            ->where('team_id', $this->team->id)
            ->where('disposition', 'pending')
            // ->whereHas('patient.exams', function ($query) {
            //     $query->where('type', 'Intake');
            // })
            ->with('patient')
            ->get()
            ->filter(function ($admission) {
                $weights = $admission->patient
                    ->getWeights()
                    ->toGrams()
                    ->pluck('weight')
                    ->all();

                $rolling = [];

                // An array of the percent difference between each weight.
                foreach ($weights as $key => $value) {
                    $rolling[] = $this->percentageDifference($weights[$key + 1] ?? $value, $value);
                }

                // Is a sum of the percent differences less than a 10 percent decrease?
                return array_sum($rolling) < -0.1;
            });
    }

    /**
     * Calculate the percent difference of two numbers.
     */
    public function percentageDifference(float $v1, float $v2): float
    {
        return ($v1 - $v2) / $v1;
    }
}
