<?php

namespace App\Models;

use App\Concerns\HasDailyTasks;
use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use App\Schedulable;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use function Illuminate\Events\queueable;

class NutritionPlan extends Model implements Schedulable
{
    use HasFactory;
    use SoftDeletes;
    use HasDailyTasks;
    use HasVersion7Uuids;
    use ValidatesOwnership;
    use LogsActivity;
    use LocksPatient;

    protected $fillable = [
        'patient_id',
        'name',
        'started_at',
        'ended_at',
        'frequency',
        'frequency_unit_id',
        'route_id',
        'description'
    ];

    protected $casts = [
        'patient_id' => 'string',
        'name' => 'string',
        'started_at' => 'date:Y-m-d',
        'ended_at' => 'date:Y-m-d',
        'frequency' => 'double',
        'frequency_unit_id' => 'integer',
        'route_id' => 'integer',
        'description' => 'string'
    ];

    protected $touches = [
        'patient',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(NutritionPlanIngredient::class);
    }

    public function frequencyUnit(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'frequency_unit_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'route_id');
    }

    public function startDate(): Attribute
    {
        return Attribute::get(
            fn () => $this->started_at
        );
    }

    public function endDate(): Attribute
    {
        return Attribute::get(
            fn () => $this->ended_at
        );
    }

    public function summaryDate(): Attribute
    {
        return Attribute::get(
            fn () => 'started_at'
        );
    }

    public function summaryBody(): Attribute
    {
        $ingredients = $this->ingredients()->orderBy('created_at')->get()->transform(
            fn ($ingredient) => "{$ingredient->quantity} {$ingredient->unit?->value} {$ingredient->ingredient}"
        )->join(', ', ', '.__('and').' ');

        $body = sprintf(
            '%s every %s by %s. %s',
            $ingredients,
            $this->frequency.' '.$this->frequencyUnit?->value,
            $this->route?->value,
            $this->description
        );

        $end = $this->ended_at instanceof Carbon ? $this->ended_at->toFormattedDateString() : __('open');

        return Attribute::get(
            fn () => Str::of($body)->trim()->squish().'. '.__('From').' '.$this->started_at->toFormattedDateString().' '.__('to').' '.$end
        );
    }

    public function badgeText(): Attribute
    {
        return Attribute::get(
            fn () => Wrmd::humanize($this),
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

    protected static function booted(): void
    {
        static::deleting(queueable(function (NutritionPlan $nutritionPlan) {
            $nutritionPlan->ingredients()->delete();
        }));
    }
}
