<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomField extends Model
{
    use HasFactory;
    use HasVersion7Uuids;
    use LocksPatient;
    use LogsActivity;
    use ValidatesOwnership;

    protected $fillable = [
        'team_id',
        'team_field_id',
        'group_id',
        'panel_id',
        'location_id',
        'type_id',
        'label',
        'options',
        'is_required',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'team_field_id' => 'integer',
        'group_id' => 'integer',
        'panel_id' => 'integer',
        'location_id' => 'integer',
        'type_id' => 'integer',
        'label' => 'string',
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
