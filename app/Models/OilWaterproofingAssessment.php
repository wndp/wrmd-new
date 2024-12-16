<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use App\Summarizable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OilWaterproofingAssessment extends Model implements Summarizable
{
    use HasFactory;
    use HasVersion7Uuids;
    use LocksPatient;
    use LogsActivity;
    use SoftDeletes;
    use ValidatesOwnership;

    protected $fillable = [
        'patient_id',
        'date_evaluated_at',
        'time_evaluated_at',
        'buoyancy_id',
        'hauled_out_id',
        'preening_id',
        'areas_wet_to_skin',
        'areas_surface_wet',
        'comments',
        'examiner',
    ];

    protected $casts = [
        'patient_id' => 'string',
        'date_evaluated_at' => 'date:Y-m-d',
        'time_evaluated_at' => 'string',
        'buoyancy_id' => 'integer',
        'hauled_out_id' => 'integer',
        'preening_id' => 'integer',
        'areas_wet_to_skin' => 'array',
        'areas_surface_wet' => 'array',
        'comments' => 'string',
        'examiner' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function buoyancy()
    {
        return $this->belongsTo(AttributeOption::class, 'buoyancy_id');
    }

    public function hauledOut()
    {
        return $this->belongsTo(AttributeOption::class, 'hauled_out_id');
    }

    public function preening()
    {
        return $this->belongsTo(AttributeOption::class, 'preening_id');
    }

    protected function evaluatedAt(): Attribute
    {
        return Attribute::get(function () {
            if (is_null($this->time_evaluated_at)) {
                return $this->date_evaluated_at?->toFormattedDayDateString();
            }

            return $this->date_evaluated_at?->setTimeFromTimeString($this->time_evaluated_at);
        });
    }

    public function summaryBody(): Attribute
    {
        return Attribute::get(function () {
            $keys = ['buoyancy_id', 'hauled_out_id', 'preening_id', 'comments', 'examiner'];

            $details[] = $this->areas_wet_to_skin ? '<strong>Areas WTS:</strong> '.implode(', ', $this->areas_wet_to_skin) : '';

            foreach ($keys as $key) {
                $details[] = $this->$key ? '<strong>'.ucwords(str_replace('_', ' ', $key)).':</strong> '.$this->$key : '';
            }

            return implode(', ', array_filter($details));
        });
    }

    public function summaryDate(): Attribute
    {
        return Attribute::get(fn () => 'evaluated_at');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
