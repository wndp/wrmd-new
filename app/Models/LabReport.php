<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use App\Summarizable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

use function Illuminate\Events\queueable;

class LabReport extends Model implements Summarizable
{
    use HasFactory;
    use HasVersion7Uuids;
    use LocksPatient;
    use LogsActivity;
    use SoftDeletes;
    use ValidatesOwnership;

    protected $fillable = [
        'patient_id',
        'accession_number',
        'analysis_facility',
        'analysis_date_at',
        'technician',
        'comments',
    ];

    protected $casts = [
        'patient_id' => 'string',
        'accession_number' => 'string',
        'analysis_facility' => 'string',
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

    public function labResult(): MorphTo
    {
        return $this->morphTo();
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

    protected static function booted(): void
    {
        static::deleting(queueable(function (LabReport $labReport) {
            $labReport->labResult()->delete();
        }));
    }
}
