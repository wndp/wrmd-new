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
    use ValidatesOwnership;
    use LogsActivity;
    use LocksPatient;

    protected $fillable = [
        'account_field_id',
        'group',
        'panel',
        'location',
        'type',
        'label',
        'options',
        'is_required',
    ];

    protected $casts = [
        'account_field_id' => 'integer',
        'group' => 'string',
        'panel' => 'string',
        'location' => 'string',
        'type' => 'string',
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
