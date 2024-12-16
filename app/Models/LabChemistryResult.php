<?php

namespace App\Models;

use App\Badgeable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LabChemistryResult extends Model implements Badgeable
{
    /** @use HasFactory<\Database\Factories\LabChemistryResultFactory> */
    use HasFactory;

    use HasVersion7Uuids;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'ast',
        'ck',
        'ggt',
        'amy',
        'alb',
        'alp',
        'alt',
        'tp',
        'glob',
        'bun',
        'chol',
        'crea',
        'ba',
        'ca',
        'ca_unit_id',
        'p',
        'p_unit_id',
        'cl',
        'cl_unit_id',
        'k',
        'k_unit_id',
        'na',
        'na_unit_id',
        'total_bilirubin',
        'ag_ratio',
        'tri',
        'nak_ratio',
        'cap_ratio',
        'glu',
        'ua',
    ];

    protected $casts = [
        'ast' => 'float',
        'ck' => 'float',
        'ggt' => 'float',
        'amy' => 'float',
        'alb' => 'float',
        'alp' => 'float',
        'alt' => 'float',
        'tp' => 'float',
        'glob' => 'float',
        'bun' => 'float',
        'chol' => 'float',
        'crea' => 'float',
        'ba' => 'float',
        'glu' => 'float',
        'ca' => 'float',
        'ca_unit_id' => 'integer',
        'p' => 'float',
        'p_unit_id' => 'integer',
        'cl' => 'float',
        'cl_unit_id' => 'integer',
        'k' => 'float',
        'k_unit_id' => 'integer',
        'na' => 'float',
        'na_unit_id' => 'integer',
        'total_bilirubin' => 'float',
        'ag_ratio' => 'float',
        'tri' => 'float',
        'nak_ratio' => 'float',
        'cap_ratio' => 'float',
        'ua' => 'float',
    ];

    public function labReport(): MorphOne
    {
        return $this->morphOne(LabReport::class, 'lab_result');
    }

    public function badgeText(): Attribute
    {
        return Attribute::get(
            fn () => __('Blood Chemistry'),
        );
    }

    public function badgeColor(): Attribute
    {
        return Attribute::get(
            fn () => 'violet',
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
