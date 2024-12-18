<?php

namespace App\Models;

use App\Concerns\HasUniqueFields;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'facility_id',
        'area',
        'enclosure',
    ];

    protected $casts = [
        'facility_id' => 'integer',
        'area' => 'string',
        'enclosure' => 'string',
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

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'patient_locations')
            ->using(PatientLocation::class)
            ->withPivot('id', 'moved_in_at', 'hours', 'comments')
            ->as('patientLocation')
            ->withTimestamps()
            ->orderByPivot('moved_in_at', 'desc')
            ->orderByPivot('created_at', 'desc');
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'facility_id');
    }

    protected function locationForHumans(): Attribute
    {
        [$homeCareId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_LOCATION_FACILITIES->value,
            AttributeOptionUiBehavior::PATIENT_LOCATION_FACILITIES_IS_HOMECARE->value,
        ]);

        return Attribute::get(
            function () use ($homeCareId) {
                $string = $this->area;

                if ($this->facility_id === $homeCareId) {
                    return $string;
                }

                return $string .= trim($this->enclosure) === '' ? '' : ', '.$this->enclosure;
            }
        );
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
