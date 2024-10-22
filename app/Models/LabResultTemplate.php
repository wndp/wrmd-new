<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LabResultTemplate extends Model
{
    use HasFactory;
    use HasVersion7Uuids;
    use LogsActivity;

    protected $fillable = [
        'name',
        'method_id',
    ];

    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'method_id' => 'integer',
    ];

    public function method(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'method_id');
    }

    public function labResultTemplateParameters(): HasMany
    {
        return $this->hasMany(LabResultTemplateParameter::class);
    }

    public function labReports(): HasMany
    {
        return $this->hasMany(LabReport::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
