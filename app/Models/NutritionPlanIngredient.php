<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class NutritionPlanIngredient extends Model
{
    use HasFactory;
    use HasVersion7Uuids;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'nutrition_plan_id',
        'quantity',
        'unit_id',
        'ingredient',
    ];

    protected $casts = [
        'nutrition_plan_id' => 'string',
        'quantity' => 'double',
        'unit_id' => 'integer',
        'ingredient' => 'string',
    ];

    protected $with = [
        'unit',
    ];

    public function nutritionPlan(): BelongsTo
    {
        return $this->belongsTo(NutritionPlan::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'unit_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
