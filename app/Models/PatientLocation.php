<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Summarizable;
use App\Support\Timezone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PatientLocation extends Model implements Summarizable
{
    use HasFactory;
    use SoftDeletes;
    use ValidatesOwnership;
    use HasVersion7Uuids;
    use LogsActivity;
    use LocksPatient;

    protected $fillable = [
        'moved_in_at',
        'facility_id',
        'area',
        'enclosure',
        'hours',
        'comments',
    ];

    protected $casts = [
        'moved_in_at' => 'datetime',
        'facility_id' => 'integer',
        'area' => 'string',
        'enclosure' => 'string',
        'hours' => 'float',
        'comments' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    protected $appends = [
        'location_for_humans',
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

    public function facility(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'facility_id');
    }

    public function summaryDate(): Attribute
    {
        return Attribute::get(fn () => 'moved_in_at');
    }

    public function summaryBody(): Attribute
    {
        return Attribute::get(
            fn () => __('Moved to :location.', [
                'location' => $this->location_for_humans
            ])." $this->comments"
        );
    }

    protected function locationForHumans(): Attribute
    {
        [$homeCareId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_LOCATION_FACILITIES->value,
            AttributeOptionUiBehavior::PATIENT_LOCATION_FACILITIES_IS_HOMECARE->value
        ]);

        return Attribute::get(
            function () use ($homeCareId) {
                $string = $this->area;

                if ($this->facility_id === $homeCareId) {
                    return $string;
                }

                return $string .= trim($this->enclosure) === '' ? '' : ', '.$this->enclosure;
            }
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
