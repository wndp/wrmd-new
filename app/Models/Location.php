<?php

namespace App\Models;

use App\Concerns\HasUniqueFields;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Location extends Model
{
    use HasFactory;
    use HasUniqueFields;
    use HasVersion7Uuids;
    use LogsActivity;

    protected $fillable = [
        'hash',
        'area',
        'enclosure',
    ];

    protected array $unique = [
        'hash',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function patients(): HasManyThrough
    {
        return $this->hasManyThrough(Patient::class, PatientLocation::class);
    }

    public function currentPatients(): Collection
    {
        [$dispositionPendingId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value,
        ]);

        return $this->patients()->where('disposition_id', $dispositionPendingId)->get();

        // return Patient::where('team_possession_id', $this->team_id)
        //     //->select('admissions.*', 'patient_locations.location_id')
        //     // ->join('patients', 'admissions.patient_id', '=', 'patients.id')
        //     // ->joinLastLocation()
        //     // ->where('disposition', 'Pending')
        //     // ->whereRelation()
        //     ->get();
        //     // ->filter(fn ($admission) =>
        //     //     (int) $admission->location_id === (int) $this->id
        //     // );
    }
}
