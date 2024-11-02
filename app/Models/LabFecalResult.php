<?php

namespace App\Models;

use App\Badgeable;
use App\Support\Wrmd;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LabFecalResult extends Model implements Badgeable
{
    /** @use HasFactory<\Database\Factories\LabFecalResultFactory> */
    use HasFactory;
    use SoftDeletes;
    use HasVersion7Uuids;
    use LogsActivity;

    protected $fillable = [
        'float_id',
        'direct_id',
        'centrifugation_id',
    ];

    protected $casts = [
        'float_id' => 'integer',
        'direct_id' => 'integer',
        'centrifugation_id' => 'integer',
    ];

    public function labReport(): MorphOne
    {
        return $this->morphOne(LabReport::class, 'lab_result');
    }

    public function float(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'float_id');
    }

    public function direct(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'direct_id');
    }

    public function centrifugation(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'centrifugation_id');
    }

    public function badgeText(): Attribute
    {
        return Attribute::get(
            fn () => __('Fecal Analysis'),
        );
    }

    public function badgeColor(): Attribute
    {
        return Attribute::get(
            fn () => 'orange',
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
