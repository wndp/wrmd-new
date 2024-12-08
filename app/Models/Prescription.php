<?php

namespace App\Models;

use App\Concerns\HasDailyTasks;
use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Schedulable;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Prescription extends Model implements Schedulable
{
    use HasFactory;
    use SoftDeletes;
    use HasDailyTasks;
    use HasVersion7Uuids;
    use ValidatesOwnership;
    use LogsActivity;
    use LocksPatient;

    protected $fillable = [
        'drug',
        'rx_started_at',
        'rx_ended_at',
        'concentration',
        'concentration_unit_id',
        'dosage',
        'dosage_unit_id',
        'loading_dose',
        'loading_dose_unit_id',
        'dose',
        'dose_unit_id',
        'frequency_id',
        'route_id',
        'is_controlled_substance',
        'instructions',
    ];

    protected $casts = [
        'rx_started_at' => 'datetime',
        'rx_ended_at' => 'datetime',
        'concentration' => 'float',
        'concentration_unit_id' => 'integer',
        'dosage' => 'float',
        'dosage_unit_id' => 'integer',
        'loading_dose' => 'float',
        'loading_dose_unit_id' => 'integer',
        'dose' => 'float',
        'dose_unit_id' => 'integer',
        'frequency_id' => 'integer',
        'route_id' => 'integer',
        'is_controlled_substance' => 'boolean',
        'instructions' => 'string',
    ];

    protected $appends = [
        // 'occurrences',
        // 'full_prescription',
        // 'is_completed',
        // 'summary_body',
        // 'start_date',
        // 'end_date',
        // 'badge_text',
        // 'badge_color',
    ];

    protected $touches = [
        'patient',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function veterinarian(): BelongsTo
    {
        return $this->belongsTo(Veterinarian::class);  //withDefault
    }

    public function doseUnit(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'dose_unit_id');
    }

    public function dosageUnit(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'dosage_unit_id');
    }

    public function concentrationUnit(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'concentration_unit_id');
    }

    public function frequency(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'frequency_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'route_id');
    }

    public function loadingDoseUnit(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'loading_dose_unit_id');
    }

    public function startDate(): Attribute
    {
        return Attribute::get(
            fn () => $this->rx_started_at
        );
    }

    public function endDate(): Attribute
    {
        return Attribute::get(
            fn () => $this->rx_ended_at
        );
    }

    // public function getFrequencyAttribute()
    // {
    //     return $this->frequency_id;
    // }

    public function summaryDate(): Attribute
    {
        return Attribute::get(
            fn () => 'rx_started_at'
        );
    }

    public function summaryBody(): Attribute
    {
        $end = $this->rx_ended_at instanceof Carbon ? $this->rx_ended_at->toFormattedDateString() : __('open');

        return Attribute::get(
            fn () => $this->fullPrescription.' '.__('From').' '.$this->rx_started_at->toFormattedDateString().' '.__('to').' '.$end
        );
    }

    public function hasOccurrenceWindowChanged(): bool
    {
        return $this->wasChanged([
            'frequency_id',
            'rx_started_at',
            'rx_ended_at',
        ]);
    }

    public function badgeText(): Attribute
    {
        return Attribute::get(
            fn () => Wrmd::humanize($this),
        );
    }

    public function badgeColor(): Attribute
    {
        return Attribute::get(
            fn () => 'blue',
        );
    }

    public function fullDose(): Attribute
    {
        return Attribute::get(
            fn () => empty($this->dose) ? '' : $this->dose.$this->doseUnit?->value,
        );
    }

    public function fullConcentration(): Attribute
    {
        return Attribute::get(
            fn () => empty($this->concentration) ? '' : $this->concentration.$this->concentrationUnit?->value,
        );
    }

    public function fullDosage(): Attribute
    {
        return Attribute::get(
            fn () => empty($this->dosage) ? '' : $this->dosage.$this->dosageUnit?->value,
        );
    }

    public function fullPrescription(): Attribute
    {
        $dose = empty($this->dose) ? '' : $this->full_dose.' '.__('of');
        $loading = empty($this->loading_dose) ? '' : '('.__('Loading Dose').' '.$this->loading_dose.$this->loadingDoseUnit?->value.')';
        $instructions = empty($this->instructions) ? '' : ' '.trim($this->instructions, '.').'.';

        return Attribute::get(
            fn () => Str::of(
                "$dose
                $this->full_concentration
                {$this->drug}
                $this->full_dosage
                {$this->route?->valuee}
                {$this->frequency?->value}"
            )->trim()->squish().trim(". $loading $instructions")
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getTotalAdministrationsAttribute()
    {
        [
            $frequencySingleDoseId,
            $frequencyPrnId,
            $frequency1DailyId,
            $frequency2DailyId,
            $frequency3DailyId,
            $frequency4DailyId,
            $frequency5DailyId,
            $frequency6DailyId,
            $frequencyQ2DaysId,
            $frequencyQ3DaysId,
            $frequencyQ4DaysId,
            $frequencyQ5DaysId,
            $frequencyQ7DaysId,
            $frequencyQ14DaysId,
            $frequencyQ21DaysId,
            $frequencyQ28DaysId,
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SINGLE_DOSE->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_PRN->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_1_DAILY->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_2_DAILY->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_3_DAILY->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_4_DAILY->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_5_DAILY->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_6_DAILY->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_3_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_4_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_5_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_7_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_14_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_21_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_28_DAYS->value],
        ]);

        if ($this->frequency_id === $frequencySingleDoseId) {
            return 1;
        }

        if (is_null($this->rx_ended_at) || $this->frequency_id === $frequencyPrnId) {
            return INF;
        }

        $days = $this->rx_started_at->diffInDays($this->rx_ended_at) + 1;

        switch ($this->frequency_id) {
            case $frequency2DailyId: return intval($days * 2);
            case $frequency3DailyId: return intval($days * 3);
            case $frequency4DailyId: return intval($days * 4);
            case $frequency5DailyId: return intval($days * 5);
            case $frequency6DailyId: return intval($days * 6);
            case $frequencyQ2DaysId: return intval(ceil($days / 2));
            case $frequencyQ3DaysId: return intval(ceil($days / 3));
            case $frequencyQ4DaysId: return intval(ceil($days / 4));
            case $frequencyQ5DaysId: return intval(ceil($days / 5));
            case $frequencyQ7DaysId: return intval(ceil($days / 7));
            case $frequencyQ14DaysId: return intval(ceil($days / 14));
            case $frequencyQ21DaysId: return intval(ceil($days / 21));
            case $frequencyQ28DaysId: return intval(ceil($days / 28));
            default: return intval($days);
        }
    }

    /**
     * Get a collection of all the administrations for the prescription.
     */
    public function getAdministrationsAttribute(): Collection
    {
        [
            $frequencyQ2DaysId,
            $frequencyQ3DaysId,
            $frequencyQ4DaysId,
            $frequencyQ5DaysId,
            $frequencyQ7DaysId,
            $frequencyQ14DaysId,
            $frequencyQ21DaysId,
            $frequencyQ28DaysId,
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_3_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_4_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_5_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_7_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_14_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_21_DAYS->value],
            [AttributeOptionName::DAILY_TASK_FREQUENCIES->value, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_28_DAYS->value],
        ]);

        $administrations = collect();

        if ($this->total_administrations === INF) {
            $this->administered_at = $this->rx_started_at;

            return $administrations->push($this);
        }

        for ($i = 0; $i < $this->total_administrations / $this->administrationsPerDay(); $i++) {
            for ($n = 0; $n < $this->administrationsPerDay(); $n++) {
                $clone = $this->replicate();

                switch ($this->frequency_id) {
                    case $frequencyQ2DaysId: $clone->administered_at = $this->rx_started_at->copy()->addDays($i * 2);
                        break;
                    case $frequencyQ3DaysId: $clone->administered_at = $this->rx_started_at->copy()->addDays($i * 3);
                        break;
                    case $frequencyQ4DaysId: $clone->administered_at = $this->rx_started_at->copy()->addDays($i * 4);
                        break;
                    case $frequencyQ5DaysId: $clone->administered_at = $this->rx_started_at->copy()->addDays($i * 5);
                        break;
                    case $frequencyQ7DaysId: $clone->administered_at = $this->rx_started_at->copy()->addDays($i * 7);
                        break;
                    case $frequencyQ14DaysId: $clone->administered_at = $this->rx_started_at->copy()->addDays($i * 14);
                        break;
                    case $frequencyQ21DaysId: $clone->administered_at = $this->rx_started_at->copy()->addDays($i * 21);
                        break;
                    case $frequencyQ28DaysId: $clone->administered_at = $this->rx_started_at->copy()->addDays($i * 28);
                        break;
                    default: $clone->administered_at = $this->rx_started_at->copy()->addDays($i);
                        break;
                }

                $administrations->push($clone);
            }
        }

        return $administrations;
    }
}
