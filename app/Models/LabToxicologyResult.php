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

class LabToxicologyResult extends Model implements Badgeable
{
    /** @use HasFactory<\Database\Factories\LabToxicologyResultFactory> */
    use HasFactory;
    use SoftDeletes;
    use HasVersion7Uuids;
    use LogsActivity;

    protected $fillable = [
        'toxin_id',
        'level',
        'level_unit_id',
        'source',
    ];

    protected $casts = [
        'toxin_id' => 'integer',
        'level' => 'float',
        'level_unit_id' => 'integer',
        'source' => 'string',
    ];

    public function labReport(): MorphOne
    {
        return $this->morphOne(LabReport::class, 'lab_result');
    }

    public function badgeText(): Attribute
    {
        return Attribute::get(
            fn () => __('Toxicology'),
        );
    }

    public function badgeColor(): Attribute
    {
        return Attribute::get(
            fn () => 'gray',
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
