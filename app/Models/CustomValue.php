<?php

namespace App\Models;

use App\Casts\CustomFieldCast;
use App\Concerns\ValidatesOwnership;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomValue extends Model
{
    /** @use HasFactory<\Database\Factories\CustomValueFactory> */
    use HasFactory;

    use HasVersion7Uuids;
    use LogsActivity;
    use Prunable;
    use ValidatesOwnership;

    protected $fillable = [
        'team_id',
        'custom_field_1',
        'custom_field_2',
        'custom_field_3',
        'custom_field_4',
        'custom_field_5',
        'custom_field_6',
        'custom_field_7',
        'custom_field_8',
        'custom_field_9',
        'custom_field_10',
        'custom_field_11',
        'custom_field_12',
        'custom_field_13',
        'custom_field_14',
        'custom_field_15',
    ];

    protected $casts = [
        'custom_field_1' => CustomFieldCast::class,
        'custom_field_2' => CustomFieldCast::class,
        'custom_field_3' => CustomFieldCast::class,
        'custom_field_4' => CustomFieldCast::class,
        'custom_field_5' => CustomFieldCast::class,
        'custom_field_6' => CustomFieldCast::class,
        'custom_field_7' => CustomFieldCast::class,
        'custom_field_8' => CustomFieldCast::class,
        'custom_field_9' => CustomFieldCast::class,
        'custom_field_10' => CustomFieldCast::class,
        'custom_field_11' => CustomFieldCast::class,
        'custom_field_12' => CustomFieldCast::class,
        'custom_field_13' => CustomFieldCast::class,
        'custom_field_14' => CustomFieldCast::class,
        'custom_field_15' => CustomFieldCast::class,
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function customable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::whereRaw('COALESCE(
            `custom_field_1`,
            `custom_field_2`,
            `custom_field_3`,
            `custom_field_4`,
            `custom_field_5`,
            `custom_field_6`,
            `custom_field_7`,
            `custom_field_8`,
            `custom_field_9`,
            `custom_field_10`,
            `custom_field_11`,
            `custom_field_12`,
            `custom_field_13`,
            `custom_field_14`,
            `custom_field_15`
        ) IS NULL');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
