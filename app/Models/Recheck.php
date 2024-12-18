<?php

namespace App\Models;

use App\Concerns\HasDailyTasks;
use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use App\Schedulable;
use App\Support\Wrmd;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Recheck extends Model implements Schedulable
{
    use HasDailyTasks;
    use HasFactory;
    use HasVersion7Uuids;
    use LocksPatient;
    use LogsActivity;
    use SoftDeletes;
    use ValidatesOwnership;

    protected $fillable = [
        'patient_id',
        'recheck_start_at',
        'recheck_end_at',
        'frequency_id',
        'assigned_to_id',
        'description',
    ];

    protected $casts = [
        'patient_id' => 'string',
        'recheck_start_at' => 'date',
        'recheck_end_at' => 'date',
        'frequency_id' => 'integer',
        'assigned_to_id' => 'integer',
        'description' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function frequency(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'frequency_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'assigned_to_id');
    }

    public function summaryBody(): Attribute
    {
        return Attribute::get(
            fn () => $this->description
        );
    }

    public function summaryDate(): Attribute
    {
        return Attribute::get(
            fn () => 'recheck_start_at'
        );
    }

    public function startDate(): Attribute
    {
        return Attribute::get(
            fn () => $this->recheck_start_at
        );
    }

    public function endDate(): Attribute
    {
        return Attribute::get(
            fn () => $this->recheck_end_at
        );
    }

    public function getFrequencyAttribute()
    {
        return $this->frequency_id;
    }

    public function badgeText(): Attribute
    {
        return Attribute::get(
            fn () => Wrmd::humanize($this).': '.$this->assignedTo?->value,
        );
    }

    public function badgeColor(): Attribute
    {
        return Attribute::get(
            fn () => 'green',
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function hasOccurrenceWindowChanged(): bool
    {
        return $this->wasChanged([
            'frequency_id',
            'recheck_start_at',
            'recheck_end_at',
        ]);
    }
}
