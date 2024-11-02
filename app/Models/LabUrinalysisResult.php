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

class LabUrinalysisResult extends Model implements Badgeable
{
    /** @use HasFactory<\Database\Factories\LabUrinalysisResultFactory> */
    use HasFactory;
    use SoftDeletes;
    use HasVersion7Uuids;
    use LogsActivity;

    protected $fillable = [
        'collection_method_id',
        'sg',
        'ph',
        'pro',
        'glu',
        'ket',
        'bili',
        'ubg',
        'nitrite',
        'bun',
        'leuc',
        'blood',
        'color',
        'turbidity_id',
        'odor_id',
        'crystals',
        'casts',
        'cells',
        'microorganisms',
    ];

    protected $casts = [
        'collection_method_id' => 'integer',
        'sg' => 'float',
        'ph' => 'float',
        'pro' => 'float',
        'glu' => 'string',
        'ket' => 'string',
        'bili' => 'string',
        'ubg' => 'string',
        'nitrite' => 'string',
        'bun' => 'float',
        'leuc' => 'string',
        'blood' => 'string',
        'color' => 'string',
        'turbidity_id' => 'integer',
        'odor_id' => 'integer',
        'crystals' => 'string',
        'casts' => 'string',
        'cells' => 'string',
        'microorganisms' => 'string',
    ];

    public function labReport(): MorphOne
    {
        return $this->morphOne(LabReport::class, 'lab_result');
    }

    public function badgeText(): Attribute
    {
        return Attribute::get(
            fn () => __('Urinalysis'),
        );
    }

    public function badgeColor(): Attribute
    {
        return Attribute::get(
            fn () => 'yellow',
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
