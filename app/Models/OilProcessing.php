<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use App\Concerns\QueriesDateRange;
use App\Concerns\ValidatesOwnership;
use App\Notifications\NotifyOwcnOfIoa;
use App\Support\Timezone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OilProcessing extends Model
{
    use HasFactory;
    use HasVersion7Uuids;
    use SoftDeletes;
    use LogsActivity;
    use LocksPatient;
    use ValidatesOwnership;

    protected $fillable = [
        'patient_id',
        'date_collected_at',
        'time_collected_at',
        'is_individual_oiled_animal',
        'collection_condition_id',
        'date_received_at_primary_care_at',
        'time_received_at_primary_care_at',
        'received_at_primary_care_by',
        'species_confirmed_by',
        'date_processed_at',
        'time_processed_at',
        'processed_by',
        'oiling_status_id',
        'oiling_percentage_id',
        'oiling_depth_id',
        'oiling_location_id',
        'color_of_oil_id',
        'oil_condition_id',
        'evidence_collected',
        'evidence_collected_by',
        'carcass_condition_id',
        'extent_of_scavenging_id',
        'comments',
    ];

    protected $casts = [
        'patient_id' => 'string',
        'date_collected_at' => 'date:Y-m-d',
        'time_collected_at' => 'string',
        'is_individual_oiled_animal' => 'boolean',
        'collection_condition_id' => 'integer',
        'date_received_at_primary_care_at' => 'date:Y-m-d',
        'time_received_at_primary_care_at' => 'string',
        'received_at_primary_care_by' => 'string',
        'species_confirmed_by' => 'string',
        'date_processed_at' => 'date:Y-m-d',
        'time_processed_at' => 'string',
        'processed_by' => 'string',
        'oiling_status_id' => 'integer',
        'oiling_percentage_id' => 'integer',
        'oiling_depth_id' => 'integer',
        'oiling_location_id' => 'integer',
        'color_of_oil_id' => 'integer',
        'oil_condition_id' => 'integer',
        'evidence_collected' => 'array',
        'evidence_collected_by' => 'string',
        'carcass_condition_id' => 'integer',
        'extent_of_scavenging_id' => 'integer',
        'comments' => 'string',
    ];

    protected $touches = [
        'patient',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    protected function collectedAt(): Attribute
    {
        return Attribute::get(function () {
            if (is_null($this->time_collected_at)) {
                return $this->date_collected_at?->toFormattedDayDateString();
            }
            return $this->date_collected_at?->setTimeFromTimeString($this->time_collected_at);
        });
    }

    protected function receivedAtPrimaryCareAt(): Attribute
    {
        return Attribute::get(function () {
            if (is_null($this->time_received_at_primary_care_at)) {
                return $this->date_received_at_primary_care_at?->toFormattedDayDateString();
            }
            return $this->date_received_at_primary_care_at?->setTimeFromTimeString($this->time_received_at_primary_care_at);
        });
    }

    protected function processedAt(): Attribute
    {
        return Attribute::get(function () {
            if (is_null($this->time_processed_at)) {
                return $this->date_processed_at?->toFormattedDayDateString();
            }
            return $this->date_processed_at?->setTimeFromTimeString($this->time_processed_at);
        });
    }

    public static function store(string $patientId, array $values, bool $isIndividualOiledAnimal = false): OilProcessing
    {
        $processing = static::firstOrNew(['patient_id' => $patientId]);

        if (! $processing->exists) {
            $processing->is_individual_oiled_animal = $isIndividualOiledAnimal;
        }

        $processing->fill($values)->save();

        return $processing;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public static function boot(): void
    {
        parent::boot();

        static::created(function ($processing) {
            if ($processing->is_individual_oiled_animal) {
                TransferIoaPatient::withChain([
                    new NotifyOwcnOfIoa($processing),
                ])->dispatch($processing);
            }
        });
    }
}
