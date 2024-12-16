<?php

namespace App\Analytics;

use App\Enums\SettingKey;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Support\Fluent;

class AnalyticFilters extends Fluent
{
    public static function defaultDatePeriod()
    {
        return 'past-7-days';
    }

    public static function defaultDateFrom()
    {
        return Carbon::now(
            Wrmd::settings(SettingKey::TIMEZONE)
        )->subDays(6)->format('Y-m-d');
    }

    public static function defaultDateTo()
    {
        return Carbon::now(
            Wrmd::settings(SettingKey::TIMEZONE)
        )->format('Y-m-d');
    }

    public static function defaultSegment()
    {
        return 'All Patients';
    }

    public static function comparePeriod()
    {
        return 'previousperiod';
    }

    public static function groupByPeriod()
    {
        return 'Day';
    }

    public function toArray()
    {
        return [
            'date_period' => $this->date_period,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'compare' => $this->compare,
            'compare_period' => $this->compare_period,
            'compare_date_from' => $this->compare_date_from,
            'compare_date_to' => $this->compare_date_to,
            'group_by_period' => $this->group_by_period,
            'limit_to_search' => $this->limit_to_search,
            'segments' => $this->segments,
            'category' => $this->category,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __get($key)
    {
        if (in_array($key, ['compare', 'limit_to_search'])) {
            return filter_var($this->get($key), FILTER_VALIDATE_BOOLEAN);
        }

        if ($this->offsetExists($key)) {
            return $this->get($key);
        }

        if ($key === 'date_period') {
            return self::defaultDatePeriod();
        }
        if ($key === 'date_from') {
            return self::defaultDateFrom();
        }
        if ($key === 'date_to') {
            return self::defaultDateTo();
        }
        if ($key === 'segments') {
            return [self::defaultSegment()];
        }
        if ($key === 'compare_period') {
            return self::comparePeriod();
        }
        if ($key === 'group_by_period') {
            return self::groupByPeriod();
        }
    }
}
