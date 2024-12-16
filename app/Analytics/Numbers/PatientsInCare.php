<?php

namespace App\Analytics\Numbers;

use App\Analytics\Contracts\Number;
use App\Enums\SettingKey;
use App\Models\Admission;
use App\Support\Wrmd;
use Carbon\Carbon;

class PatientsInCare extends Number
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $inCare = Admission::inCareOnDate($this->team, Carbon::now(Wrmd::settings(SettingKey::TIMEZONE)))->count();

        $other = $this->filters->compare
            ? Admission::inCareOnDate($this->team, Carbon::now(Wrmd::settings(SettingKey::TIMEZONE))->subDay())->count()
            : null;

        $this->calculatePercentageDifference($inCare, $other);

        $this->now = $inCare;
        $this->prev = $other;
    }
}
