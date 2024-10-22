<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use App\Enums\LabReportType;
use App\Summarizable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LabReport extends Model implements Summarizable
{
    use HasFactory;
    use SoftDeletes;
    use HasVersion7Uuids;
    use ValidatesOwnership;
    use LogsActivity;
    use LocksPatient;

    protected $fillable = [
        'type',
        'storage_analysis_facility_id',
        'accession_number',
        'lab_result_template_id',
        'analysis_date_at',
        'technician',
        'comments',
    ];

    protected $casts = [
        'type' => LabReportType::class,
        'storage_analysis_facility_id' => 'integer',
        'accession_number' => 'string',
        'lab_result_template_id' => 'integer',
        'analysis_date_at' => 'date',
        'technician' => 'string',
        'comments' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function labResultTemplate(): BelongsTo
    {
        return $this->belongsTo(LabResultTemplate::class);
    }

    public function storageAnalysisFacility(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'storage_analysis_facility_id');
    }

    public function labResults(): HasMany
    {
        return $this->hasMany(LabResult::class);
    }

    public function summaryDate(): Attribute
    {
        return Attribute::get(
            fn () => 'analysis_date_at'
        );
    }

    public function summaryBody(): Attribute
    {
        return Attribute::get(
            fn () => ''
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
