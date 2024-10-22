<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LabResult extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasVersion7Uuids;
    use LogsActivity;

    protected $fillable = [
        'lab_report_id',
        'lab_result_template_parameter_id',
        'value_type',
        'value',
    ];

    protected $casts = [
        'lab_report_id' => 'string',
        'lab_result_template_parameter_id' => 'string',
        'value_type' => SampleResultAmountTypes::class,
        'value' => 'string'
    ];

    public function labReport(): BelongsTo
    {
        return $this->belongsTo(LabReport::class);
    }

    public function labResultTemplateParameter(): BelongsTo
    {
        return $this->belongsTo(LabResultTemplateParameter::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
