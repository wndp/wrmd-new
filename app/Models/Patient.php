<?php

namespace App\Models;

use App\Actions\ClonePatientRelations;
use App\Concerns\HasSpatial;
use App\Concerns\InteractsWithMedia;
use App\Concerns\JoinsTablesToPatients;
use App\Concerns\LocksPatient;
use App\Concerns\QueriesDateRange;
use App\Concerns\ValidatesOwnership;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Scopes\VoidedScope;
use App\Repositories\AdministrativeDivision;
use App\Support\Timezone;
use App\ValueObjects\SingleStorePoint;
use Carbon\Carbon;
use CommerceGuys\Addressing\Address;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;

class Patient extends Model implements HasMedia
{
    use HasFactory;
    use HasSpatial;
    use HasVersion7Uuids;
    use InteractsWithMedia;
    use JoinsTablesToPatients;
    use LocksPatient;
    use LogsActivity;
    use QueriesDateRange;
    use ValidatesOwnership;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new VoidedScope);
    }

    protected $fillable = [
        'team_possession_id',
        'is_resident',
        'voided_at',
        'locked_at',
        'taxon_id',
        'common_name',
        'morph_id',
        'date_admitted_at',
        'time_admitted_at',
        'admitted_by',
        'transported_by',
        'found_at',
        'address_found',
        'city_found',
        'subdivision_found',
        'postal_code_found',
        'county_found',
        'coordinates_found',
        'reason_for_admission',
        'care_by_rescuer',
        'notes_about_rescue',
        'diagnosis',
        'band',
        'microchip_number',
        'reference_number',
        'name',
        //'keywords',
        'disposition_id',
        'transfer_type_id',
        'release_type_id',
        'dispositioned_at',
        'disposition_address',
        'disposition_city',
        'disposition_subdivision',
        'disposition_postal_code',
        'disposition_county',
        'disposition_coordinates',
        'reason_for_disposition',
        'dispositioned_by',
        'is_carcass_saved',
        'is_criminal_activity',
    ];

    protected $casts = [
        'team_possession_id' => 'integer',
        'is_resident' => 'boolean',
        'voided_at' => 'datetime',
        'locked_at' => 'datetime',
        'taxon_id' => 'integer',
        'common_name' => 'string',
        'morph_id' => 'integer',
        'date_admitted_at' => 'date:Y-m-d',
        'time_admitted_at' => 'string',
        'admitted_by' => 'string',
        'transported_by' => 'string',
        'found_at' => 'date',
        'address_found' => 'string',
        'city_found' => 'string',
        'subdivision_found' => 'string',
        'postal_code_found' => 'string',
        'county_found' => 'string',
        'coordinates_found' => SingleStorePoint::class,
        'reason_for_admission' => 'string',
        'care_by_rescuer' => 'string',
        'notes_about_rescue' => 'string',
        'diagnosis' => 'string',
        'band' => 'string',
        'microchip_number' => 'string',
        'reference_number' => 'string',
        'name' => 'string',
        'disposition_id' => 'integer',
        'transfer_type_id' => 'integer',
        'release_type_id' => 'integer',
        'dispositioned_at' => 'date',
        'disposition_address' => 'string',
        'disposition_city' => 'string',
        'disposition_subdivision' => 'string',
        'disposition_postal_code' => 'string',
        'disposition_county' => 'string',
        'disposition_coordinates' => SingleStorePoint::class,
        'reason_for_disposition' => 'string',
        'dispositioned_by' => 'string',
        'is_carcass_saved' => 'boolean',
        'is_criminal_activity' => 'boolean',
    ];

    protected $appends = [
        'days_in_care',
        'common_name_formatted',
        'admitted_at',
        'admitted_at_for_humans',
    ];

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function expenseTransactions(): HasMany
    {
        return $this->hasMany(ExpenseTransaction::class);
    }

    public function incident(): HasOne
    {
        return $this->hasOne(Incident::class);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'patient_locations')
            ->using(PatientLocation::class)
            ->withPivot('id', 'moved_in_at', 'hours', 'comments')
            ->as('patientLocation')
            ->withTimestamps()
            ->orderByPivot('moved_in_at', 'desc')
            ->orderByPivot('created_at', 'desc');
    }

    public function possession(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_possession_id');
    }

    public function rescuer(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'rescuer_id');
    }

    public function taxon(): BelongsTo
    {
        return $this->belongsTo(Taxon::class);
    }

    public function necropsy(): HasOne
    {
        return $this->hasOne(Necropsy::class);
    }

    public function banding(): HasOne
    {
        return $this->hasOne(Banding::class);
    }

    public function morphometric(): HasOne
    {
        return $this->hasOne(Morphometric::class);
    }

    public function careLogs(): HasMany
    {
        return $this->hasMany(CareLog::class);
    }

    public function rechecks(): HasMany
    {
        return $this->hasMany(Recheck::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function nutritionPlans(): HasMany
    {
        return $this->hasMany(NutritionPlan::class);
    }

    public function oilProcessing(): HasOne
    {
        return $this->hasOne(OilProcessing::class);
    }

    public function oilWashes(): HasMany
    {
        return $this->hasMany(OilWash::class);
    }

    public function oilWaterproofingAssessments(): HasMany
    {
        return $this->hasMany(OilWaterproofingAssessment::class);
    }

    public function labReports(): HasMany
    {
        return $this->hasMany(LabReport::class);
    }

    public function labFecalResults(): MorphToMany
    {
        return $this->morphedByMany(LabFecalResult::class, 'lab_result', 'lab_reports')->withPivot([
            'accession_number',
            'analysis_facility',
            'analysis_date_at',
            'technician',
            'comments',
        ]);
    }

    public function labCbcResults(): MorphToMany
    {
        return $this->morphedByMany(LabCbcResult::class, 'lab_result', 'lab_reports')->withPivot([
            'accession_number',
            'analysis_facility',
            'analysis_date_at',
            'technician',
            'comments',
        ]);
    }

    public function labCytologyResults(): MorphToMany
    {
        return $this->morphedByMany(LabCytologyResult::class, 'lab_result', 'lab_reports')->withPivot([
            'accession_number',
            'analysis_facility',
            'analysis_date_at',
            'technician',
            'comments',
        ]);
    }

    public function labChemistryResults(): MorphToMany
    {
        return $this->morphedByMany(LabChemistryResult::class, 'lab_result', 'lab_reports')->withPivot([
            'accession_number',
            'analysis_facility',
            'analysis_date_at',
            'technician',
            'comments',
        ]);
    }

    public function labUrinalysisResults(): MorphToMany
    {
        return $this->morphedByMany(LabUrinalysisResult::class, 'lab_result', 'lab_reports')->withPivot([
            'accession_number',
            'analysis_facility',
            'analysis_date_at',
            'technician',
            'comments',
        ]);
    }

    public function labToxicologyResults(): MorphToMany
    {
        return $this->morphedByMany(LabToxicologyResult::class, 'lab_result', 'lab_reports')->withPivot([
            'accession_number',
            'analysis_facility',
            'analysis_date_at',
            'technician',
            'comments',
        ]);
    }

    public function morph(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'morph_id');
    }

    public function disposition(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'disposition_id');
    }

    public function releaseType(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'release_type_id');
    }

    public function transferType(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'transfer_type_id');
    }

    protected function admittedAt(): Attribute
    {
        return Attribute::get(function () {
            if (is_null($this->time_admitted_at)) {
                return $this->date_admitted_at->toFormattedDayDateString();
            }

            return $this->date_admitted_at->setTimeFromTimeString($this->time_admitted_at);
        });
    }

    protected function admittedAtForHumans(): Attribute
    {
        return Attribute::get(function () {
            if (is_null($this->time_admitted_at)) {
                return $this->date_admitted_at->translatedFormat(config('wrmd.date_format'));
            }

            return Timezone::convertFromUtcToLocal($this->date_admitted_at->setTimeFromTimeString($this->time_admitted_at))
                ?->translatedFormat(config('wrmd.date_time_format'));
        });
    }

    protected function commonNameFormatted(): Attribute
    {
        return Attribute::get(
            fn () => Arr::first(
                array_filter(Arr::wrap(event('commonNameFormatted', $this))),
                null,
                $this->common_name
            )
        );
    }

    /**
     * Scope patients that are unrecognized to WRMD.
     */
    public function scopeWhereUnrecognized(Builder $query): Builder
    {
        return $query->whereNotIn('patients.common_name', ['Void', 'UNBI', 'Unidentified', 'Unknown'])
            ->where('patients.common_name', 'not like', 'Unknown%')
            ->whereNull('patients.taxon_id');
    }

    /**
     * Scope patients that are misidentified to WRMD.
     */
    public function scopeWhereMisidentified(Builder $query): Builder
    {
        $wildAlertDbName = config('database.connections.wildalert.database');

        return $query->whereNotIn('patients.common_name', ['Void', 'UNBI', 'Unidentified', 'Unknown'])
            ->where('patients.common_name', 'not like', 'Unknown%')
            ->whereNotNull('patients.taxon_id')
            ->whereNotIn('patients.common_name', function ($query) use ($wildAlertDbName) {
                $query->select("$wildAlertDbName.common_names.common_name")
                    ->from("$wildAlertDbName.common_names")
                    ->join('patients as p2', "$wildAlertDbName.common_names.taxon_id", '=', 'p2.taxon_id')
                    ->whereRaw('patients.id = p2.id');
            })
            ->whereNotIn('patients.common_name', function ($query) use ($wildAlertDbName) {
                $query->select("$wildAlertDbName.taxa.alpha_code")
                    ->from("$wildAlertDbName.taxa")
                    ->join('patients as p2', "$wildAlertDbName.taxa.id", '=', 'p2.taxon_id')
                    ->whereRaw('patients.id = p2.id')
                    ->whereNotNull("$wildAlertDbName.taxa.alpha_code");
            })
            ->whereNotIn('patients.common_name', function ($query) use ($wildAlertDbName) {
                $query->select("$wildAlertDbName.common_names.alpha_code")
                    ->from("$wildAlertDbName.common_names")
                    ->join('patients as p2', "$wildAlertDbName.common_names.taxon_id", '=', 'p2.taxon_id')
                    ->whereRaw('patients.id = p2.id')
                    ->whereNotNull("$wildAlertDbName.common_names.alpha_code");
            });
    }

    public function isUnrecognized(): bool
    {
        return is_null($this->locked_at) && ! Str::contains($this->common_name, ['Void', 'UNBI', 'Unidentified', 'Unknown'], true);
    }

    protected function daysInCare(): Attribute
    {
        [$dispositionPendingId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value,
        ]);

        return Attribute::get(
            fn () => intval(
                $this->disposition_id !== $dispositionPendingId && $this->dispositioned_at instanceof Carbon
                    ? $this->date_admitted_at->diffInDays($this->dispositioned_at) + 1
                    : ($this->date_admitted_at instanceof Carbon ? $this->date_admitted_at->diffInDays() + 1 : null)
            )
        );
    }

    public function clone(): Patient
    {
        $new = $this->replicate();
        $new->save();

        ClonePatientRelations::run($this, $new);

        return $new;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getCoordinatesFoundAddress(): Address
    {
        return (new Address)
            ->withCountryCode(app(AdministrativeDivision::class)->alpha2CountryCode())
            ->withAdministrativeArea($this->subdivision_found ?: '')
            ->withLocality($this->city_found ?: '')
            ->withAddressLine1($this->address_found ?: '')
            ->withPostalCode($this->postal_code_found ?: '');
    }
}
