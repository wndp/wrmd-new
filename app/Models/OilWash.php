<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OilWash extends Model
{
    use HasFactory;
    use HasVersion7Uuids;
    use LocksPatient;
    use LogsActivity;
    use SoftDeletes;
    use ValidatesOwnership;

    protected $fillable = [
        'date_washed_at',
        'time_washed_at',
        'pre_treatment_id',
        'pre_treatment_duration',
        'wash_type_id',
        'wash_duration',
        'detergent_id',
        'rinse_duration',
        'washer',
        'handler',
        'rinser',
        'dryer',
        'drying_method_id',
        'drying_duration',
        'observations',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'patient_id' => 'string',
        'date_washed_at' => 'date:Y-m-d',
        'time_washed_at' => 'string',
        'pre_treatment_id' => 'integer',
        'pre_treatment_duration' => 'float',
        'wash_type_id' => 'integer',
        'wash_duration' => 'float',
        'detergent_id' => 'integer',
        'rinse_duration' => 'float',
        'washer' => 'string',
        'handler' => 'string',
        'rinser' => 'string',
        'dryer' => 'string',
        'drying_method_id' => 'integer',
        'drying_duration' => 'float',
        'observations' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function preTreatment()
    {
        return $this->belongsTo(AttributeOption::class, 'pre_treatment_id');
    }

    public function washType()
    {
        return $this->belongsTo(AttributeOption::class, 'wash_type_id');
    }

    public function detergent()
    {
        return $this->belongsTo(AttributeOption::class, 'detergent_id');
    }

    public function dryingMethod()
    {
        return $this->belongsTo(AttributeOption::class, 'drying_method_id');
    }

    protected function washedAt(): Attribute
    {
        return Attribute::get(function () {
            if (is_null($this->time_washed_at)) {
                return $this->date_washed_at?->toFormattedDayDateString();
            }

            return $this->date_washed_at?->setTimeFromTimeString($this->time_washed_at);
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
