<?php

namespace App\Analytics\Concerns;

use App\Analytics\ChronologicalCollection;
use Carbon\Carbon;

trait GroupsDataByTimePeriod
{
    public function groupByTimePeriod($timePeriod)
    {
        return new static(
            $this->groupBy($this->timePeriodCallback($timePeriod))
        );
    }

    public function groupByTimePeriodAndGroups($timePeriod)
    {
        return $this->map(function ($collection, $name) use ($timePeriod) {
            return new static(
                $collection->groupBy($collection->timePeriodCallback($timePeriod))
            );
        });

        //$groups[] = $this->timePeriodCallback($timePeriod);

        // $groups[] = function ($data) {
        //     return $data->group;
        // };

        //return new ChronologicalCollection($this->groupBy($groups));
    }

    public function timePeriodCallback($timePeriod)
    {
        switch ($timePeriod) {
            case 'Year':
                $format = 'Y';
                $interval = '1 year';
                $callback = function ($data) use ($format) {
                    return Carbon::parse($data->date)->format($format);
                };
                break;

            case 'Quarter':
                $format = 'M d, Y';
                $interval = '3 months';
                $callback = function ($data) use ($format) {
                    $date = Carbon::parse($data->date);

                    return $date->startOfQuarter()->format($format).' - '.$date->endOfQuarter()->format($format);
                };
                break;

            case 'Month':
                $format = 'M Y';
                $interval = '1 month';
                $callback = function ($data) use ($format) {
                    return Carbon::parse($data->date)->format($format);
                };
                break;

            case 'Week':
                $format = 'M d, Y';
                $interval = '1 week';
                $callback = function ($data) use ($format) {
                    $date = Carbon::parse($data->date);

                    return $date->modify('this week')->format($format).' - '.$date->modify('this week +6 days')->format($format);
                };
                break;

            case 'Day':
            default:
                $format = 'M d, Y';
                $interval = '1 day';
                $callback = function ($data) use ($format) {
                    return Carbon::parse($data->date)->format($format);
                };
                break;
        }

        return $callback;
    }
}
