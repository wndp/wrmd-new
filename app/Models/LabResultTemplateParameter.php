<?php

namespace App\Models;

use App\Enums\DataType;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LabResultTemplateParameter extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasVersion7Uuids;
    use LogsActivity;

    protected $fillable = [
        'lab_result_template_id',
        'parameter_id',
        'unit_id',
        'data_type',
        'sort_order',
    ];

    protected $casts = [
        'id' => 'string',
        'lab_result_template_id' => 'string',
        'parameter_id' => 'integer',
        'unit_id' => 'integer',
        'data_type' => DataType::class,
        'sort_order' => 'integer',
    ];

    public function labResultTemplate(): BelongsTo
    {
        return $this->belongsTo(LabResultTemplate::class);
    }

    public function parameter(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'parameter_id');
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
