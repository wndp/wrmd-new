<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use App\Summarizable;
use App\Support\Timezone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PatientLocation extends Pivot implements Summarizable
{
    use HasFactory;
    use HasVersion7Uuids;
    use LocksPatient;
    use LogsActivity;
    use SoftDeletes;
    use ValidatesOwnership;

    protected $table = 'patient_locations';

    protected $fillable = [
        'patient_id',
        'location_id',
        'moved_in_at',
        'hours',
        'comments',
    ];

    protected $casts = [
        'patient_id' => 'string',
        'location_id' => 'string',
        'moved_in_at' => 'datetime',
        'hours' => 'float',
        'comments' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    protected $appends = [
        //'location_for_humans',
        //'moved_in_at_for_humans',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function summaryDate(): Attribute
    {
        return Attribute::get(fn () => 'moved_in_at');
    }

    public function summaryBody(): Attribute
    {
        return Attribute::get(
            fn () => __('Moved to :location.', [
                'location' => $this->location->location_for_humans,
            ])." $this->comments"
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // protected function movedInAtForHumans(): Attribute
    // {
    //     return Attribute::get(
    //         fn () => Timezone::convertFromUtcToLocal($this->moved_in_at)->toDayDateTimeString(),
    //     );
    // }
}
