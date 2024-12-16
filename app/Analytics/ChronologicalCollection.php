<?php

namespace App\Analytics;

use App\Analytics\Concerns\GroupsDataByTimePeriod;
use App\Analytics\Concerns\HandleAggregates;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class ChronologicalCollection extends Collection
{
    use GroupsDataByTimePeriod;
    use HandleAggregates;

    /**
     * Pad the collection's items with missing dates within the provided period.
     */
    public function padDates(CarbonPeriod $period): static
    {
        // If the collection is empty then prevent unnecessary padding to inflate the collection.
        if ($this->isEmpty()) {
            return $this;
        }

        $data = $this->sortBy('date');

        // If the collection is the same length as the period length then assume no padding is needed.
        if ($data->count() === $period->count()) {
            return $data->values();
        }

        $union = (new Collection($period->toArray()))->mapWithKeys(function ($carbon) {
            return [$carbon->format('Y-m-d') => new DataSet([
                'date' => $carbon->format('Y-m-d'),
                'aggregate' => 0,
            ])];
        });

        if ($this->hasSecondLevelGrouping()) {
            return $data->groupBy('subgroup')
                ->map(function ($collection) use ($union) {
                    return $collection->unionDatedCollections($union);
                });
        }

        return $this->unionDatedCollections($union);
    }

    /**
     * Pad the collection's items with missing comparative dates by first building a period
     * beginning with a date to compare from to the give length of days.
     */
    public function padComparativeDates(string $dateFrom, int $length): static
    {
        $period = new CarbonPeriod(
            Carbon::parse($dateFrom),
            Carbon::parse($dateFrom)->addDays($length - 1)
        );

        return $this->padDates($period);
    }

    /**
     * Union the collection with a collection of dates.
     */
    private function unionDatedCollections(Collection $union): static
    {
        return $this->keyBy('date')
            ->union($union)
            ->sortBy('date')
            ->values();
    }

    /**
     * Determine if the collection has a designated subgroup attribute.
     */
    private function hasSecondLevelGrouping(): bool
    {
        return $this->isNotEmpty() && $this->first()->offsetExists('subgroup');
    }
}
