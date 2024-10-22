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

class OilConditioning extends Model implements Summarizable
{
    use HasFactory;
    use HasVersion7Uuids;
    use SoftDeletes;
    use LogsActivity;
    use LocksPatient;
    use ValidatesOwnership;

    protected $fillable = [
        'patient_id',
        'evaluated_at',
        'buoyancy_id',
        'hauled_out_id',
        'preening_id',
        'is_self_feeding',
        'is_flighted',
        'areas_wet_to_skin',
        'observations',
        'examiner',
    ];

    protected $casts = [
        'patient_id' => 'string',
        'evaluated_at' => 'datetime',
        'buoyancy_id' => 'integer',
        'hauled_out_id' => 'integer',
        'preening_id' => 'integer',
        'is_self_feeding' => 'boolean',
        'is_flighted' => 'boolean',
        'areas_wet_to_skin' => 'array',
        'observations' => 'string',
        'examiner' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function summaryBody(): Attribute
    {
        return Attribute::get(function () {
                $keys = ['buoyancy_id', 'hauled_out_id', 'preening_id', 'observations', 'examiner'];

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
