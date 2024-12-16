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

class LabCytologyResult extends Model implements Badgeable
{
    /** @use HasFactory<\Database\Factories\LabCytologyResultFactory> */
    use HasFactory;

    use HasVersion7Uuids;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'source',
    ];

    protected $casts = [
        'source' => 'string',
    ];

    public function labReport(): MorphOne
    {
        return $this->morphOne(LabReport::class, 'lab_result');
    }

    public function badgeText(): Attribute
    {
        return Attribute::get(
            fn () => __('Cytology Analysis'),
        );
    }

    public function badgeColor(): Attribute
    {
        return Attribute::get(
            fn () => 'green',
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
