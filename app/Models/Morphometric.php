<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Summarizable;
use App\Weighable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Morphometric extends Model implements Summarizable, Weighable
{
    use HasFactory;
    use HasVersion7Uuids;
    use LocksPatient;
    use LogsActivity;
    use SoftDeletes;
    use ValidatesOwnership;

    protected $fillable = [
        'patient_id',
        'measured_at',
        'bill_length',
        'bill_width',
        'bill_depth',
        'head_bill_length',
        'culmen',
        'exposed_culmen',
        'wing_chord',
        'flat_wing',
        'tarsus_length',
        'middle_toe_length',
        'toe_pad_length',
        'hallux_length',
        'tail_length',
        'weight',
        'samples_collected',
        'remarks',
    ];

    protected $casts = [
        'measured_at' => 'datetime',
        'bill_length' => 'float',
        'bill_width' => 'float',
        'bill_depth' => 'float',
        'head_bill_length' => 'float',
        'culmen' => 'float',
        'exposed_culmen' => 'float',
        'wing_chord' => 'float',
        'flat_wing' => 'float',
        'tarsus_length' => 'float',
        'middle_toe_length' => 'float',
        'toe_pad_length' => 'float',
        'hallux_length' => 'float',
        'tail_length' => 'float',
        'weight' => 'float',
        'samples_collected' => 'array',
        'remarks' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function summaryWeight(): Attribute
    {
        return Attribute::get(fn () => $this->weight);
    }

    public function summaryWeightUnitId(): Attribute
    {
        [$gWeightId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::EXAM_WEIGHT_UNITS->value,
            AttributeOptionUiBehavior::EXAM_WEIGHT_UNITS_IS_G->value,
        ]);

        return Attribute::get(fn () => $gWeightId);
    }

    public function summaryBody(): Attribute
    {
        return Attribute::get(fn () => '');
    }

    public function summaryDate(): Attribute
    {
        return Attribute::get(fn () => 'measured_at');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
