<?php

namespace App\Models;

use App\Concerns\ValidatesOwnership;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Communication extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasVersion7Uuids;
    use ValidatesOwnership;
    use LogsActivity;

    protected $fillable = [
        'incident_id',
        'communication_at',
        'communication_by',
        'communication',
    ];

    protected $casts = [
        'incident_id' => 'integer',
        'communication_at' => 'datetime',
        'communication_by' => 'string',
        'communication' => 'string',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
