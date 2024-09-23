<?php

namespace App\Models;

use App\Concerns\HasDailyTasks;
use App\Concerns\ValidatesOwnership;
use App\Schedulable;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Prescription extends Model implements Schedulable
{
    use HasFactory;
    use SoftDeletes;
    use HasDailyTasks;
    use ValidatesOwnership;

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

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
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
            fn () => 'RX: '.$this->fullPrescription.' '.__('From').' '.$this->rx_started_at->toFormattedDateString().' '.__('to').' '.$end
        );
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
            fn () => Str::of("$dose
                $this->full_concentration
                {$this->drug}
                $this->full_dosage
                {$this->route?->valuee}
                {$this->frequency?->value}"
            )->trim()->squish().trim(". $loading $instructions")
        );
    }
}
