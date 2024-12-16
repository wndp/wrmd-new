<?php

namespace App\Concerns;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\SettingKey;
use App\Jobs\DeleteRecordedDailyTasks;
use App\Jobs\UpdateRecordedDailyTasks;
use App\Models\DailyTask;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDailyTasks
{
    public static function bootHasDailyTasks()
    {
        static::updated(
            fn ($model) => $model->hasOccurrenceWindowChanged() && UpdateRecordedDailyTasks::dispatch($model)
        );

        static::deleted(
            fn ($model) => DeleteRecordedDailyTasks::dispatch($model)
        );
    }

    /**
     * Get all of the model's recorded tasks.
     */
    public function recordedTasks(): MorphMany
    {
        return $this->morphMany(DailyTask::class, 'tasks', 'task_type', 'task_id');
    }

    /**
     * Determine if a prescription is due on a given date.
     *
     * @param  string|DateTime  $date
     */
    public function isDueOn($date): bool
    {
        if (! $date instanceof \DateTime) {
            $date = is_numeric($date) ? '@'.$date : $date;
            $date = new Carbon($date);
        }

        $startDate = $this->start_date->shiftTimezone(Wrmd::settings(SettingKey::TIMEZONE));

        // If the start date is the same as the date in question then it is due.
        if ($startDate->isSameDay($date)) {
            return true;
        }

        // If the end date has passed then it is not due.
        if ($this->hasDatePast($this->end_date)) {
            return false;
        }

        $daysSinceStartDate = $startDate->diffInDays($date);

        $frequencyIds = $this->getFrequencyIds();

        if (in_array($this->frequency_id, $frequencyIds['scatteredDaysIds'])) {
            switch ($this->frequency_id) {
                case $frequencyIds['q2dId']:  return $daysSinceStartDate % 2 === 0;
                case $frequencyIds['q3dId']:  return $daysSinceStartDate % 3 === 0;
                case $frequencyIds['q4dId']:  return $daysSinceStartDate % 4 === 0;
                case $frequencyIds['q5dId']:  return $daysSinceStartDate % 5 === 0;
                case $frequencyIds['q7dId']:  return $daysSinceStartDate % 7 === 0;
                case $frequencyIds['q14dId']: return $daysSinceStartDate % 14 === 0;
                case $frequencyIds['q21dId']: return $daysSinceStartDate % 21 === 0;
                case $frequencyIds['q28dId']: return $daysSinceStartDate % 28 === 0;
                default: return false;
            }
        }

        // If the start date has passed then it is due today
        if ($daysSinceStartDate > 0) {
            return true;
        }

        // If all else fails
        return false;
    }

    /**
     * Determine if a prescription is due today.
     */
    public function isDueToday(): bool
    {
        return $this->isDueOn(now(Wrmd::settings(SettingKey::TIMEZONE)));
    }

    /**
     * Get the number of administrations to be given per day.
     */
    public function administrationsPerDay(): int
    {
        $frequencyIds = $this->getFrequencyIds();

        return match ($this->frequency_id) {
            $frequencyIds['sixXdayId'] => 6,
            $frequencyIds['fiveXdayId'] => 5,
            $frequencyIds['qidId'] => 4,
            $frequencyIds['tidId'] => 3,
            $frequencyIds['bidId'] => 2,
            default => 1
        };
    }

    public function getOccurrencesAttribute(): int
    {
        return $this->administrationsPerDay();
    }

    /**
     * Has the provided date past the current date.
     * Carbon isPast() compares datetime and can give false positives.
     *
     * @param  mixed  $date
     */
    private function hasDatePast($date): bool
    {
        if (! $date instanceof Carbon) {
            return false;
        }

        return $date->shiftTimezone(Wrmd::settings(SettingKey::TIMEZONE))->format('Y-m-d') < Carbon::now(Wrmd::settings(SettingKey::TIMEZONE))->format('Y-m-d');
    }

    private function getFrequencyIds()
    {
        [
            $singleDoseId,
            $q2dId,
            $q3dId,
            $q4dId,
            $q5dId,
            $q7dId,
            $q14dId,
            $q21dId,
            $q28dId,
            $scatteredDaysIds,
            $bidId,
            $tidId,
            $qidId,
            $fiveXdayId,
            $sixXdayId,
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SINGLE_DOSE->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_3_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_4_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_5_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_7_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_14_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_21_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_28_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SCATTERED_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_2_DAILY->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_3_DAILY->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_4_DAILY->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_5_DAILY->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_6_DAILY->value],
        ]);

        return compact(
            'singleDoseId',
            'q2dId',
            'q3dId',
            'q4dId',
            'q5dId',
            'q7dId',
            'q14dId',
            'q21dId',
            'q28dId',
            'scatteredDaysIds',
            'bidId',
            'tidId',
            'qidId',
            'fiveXdayId',
            'sixXdayId',
        );
    }
}
