<?php

namespace App\Models;

use App\Concerns\ValidatesOwnership;
use App\Weighable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Morphometric extends Model implements Weighable
{
    use HasFactory;
    use SoftDeletes;
    use ValidatesOwnership;
    use HasVersion7Uuids;

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
        'remarks' => 'string'
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
        return Attribute::get(fn () => 'g');
    }
}
