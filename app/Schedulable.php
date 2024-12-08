<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Schedulable extends Summarizable, Badgeable
{
    /**
     * Get the schedulable's "start date".
     *
     * @return Carbon
     */
    public function startDate(): Attribute;

    /**
     * Get the schedulable's "end date".
     *
     * @return Carbon|null
     */
    public function endDate(): Attribute;

    /**
     * Get the schedulable's frequency.
     *
     * @return string|null
     */
    //public function getFrequencyAttribute();

    /**
     * Get the schedulable's number of daily occurrences.
     */
    public function getOccurrencesAttribute(): int;

    /**
     * Get all of the schedulable's recorded tasks.
     */
    public function recordedTasks(): MorphMany;

    /**
     * Determine if the schedulable is due on the provided date.
     *
     * @param  sting|\DateTime  $date
     */
    public function isDueOn($date): bool;

    /**
     * Determine if the schedulable's occurrence time frame window has changed
     */
    public function hasOccurrenceWindowChanged(): bool;
}
