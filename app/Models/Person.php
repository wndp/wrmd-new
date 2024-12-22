<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use App\Concerns\QueriesDateRange;
use App\Concerns\QueriesOneOfMany;
use App\Concerns\ValidatesOwnership;
use App\Enums\PhoneFormat;
use App\Repositories\AdministrativeDivision;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Person extends Model
{
    use HasFactory;
    use HasVersion7Uuids;
    use LocksPatient;
    use LogsActivity;
    use QueriesDateRange;
    use QueriesOneOfMany;
    use SoftDeletes;
    use ValidatesOwnership;

    protected $fillable = [
        'team_id',
        'entity_id',
        'organization',
        'first_name',
        'last_name',
        'phone',
        'phone_e164',
        'phone_normalized',
        'phone_national',
        'alternate_phone',
        'alternate_phone_e164',
        'alternate_phone_normalized',
        'alternate_phone_national',
        'email',
        'country',
        'subdivision',
        'city',
        'address',
        'postal_code',
        'county',
        'notes',
        'no_solicitations',
        'is_volunteer',
        'is_member',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'entity_id' => 'integer',
        'organization' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'phone' => 'string',
        'phone_normalized' => 'string',
        'phone_e164' => 'string',
        'phone_national' => 'string',
        'alternate_phone' => 'string',
        'alternate_phone_normalized' => 'string',
        'alternate_phone_e164' => 'string',
        'alternate_phone_national' => 'string',
        'email' => 'string',
        'country' => 'string',
        'subdivision' => 'string',
        'city' => 'string',
        'address' => 'string',
        'postal_code' => 'string',
        'county' => 'string',
        'notes' => 'string',
        'no_solicitations' => 'boolean',
        'is_volunteer' => 'boolean',
        'is_member' => 'boolean',
    ];

    protected $appends = [
        'full_name',
        'identifier',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'rescuer_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'reporting_party_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function entity(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'entity_id');
    }

    // public function phone(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => app(AdministrativeDivision::class)->phoneNumber($value),
    //         set: fn ($value) => preg_replace('/[^0-9]/', '', $value)
    //     );
    // }

    // public function alternatePhone(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => app(AdministrativeDivision::class)->phoneNumber($value),
    //         set: fn ($value) => preg_replace('/[^0-9]/', '', $value)
    //     );
    // }

    public function fullName(): Attribute
    {
        return Attribute::get(
            fn () => $this->first_name.' '.$this->last_name
        );
    }

    public function identifier(): Attribute
    {
        $parts = array_filter(array_map('trim', [$this->organization, $this->first_name.' '.$this->last_name]));

        return Attribute::get(
            fn () => implode(', ', $parts) ?: 'Unidentified'
        );
    }

    protected function isRescuer(): Attribute
    {
        return Attribute::get(
            fn () => $this->patients->count() > 0,
        );
    }

    protected function isReportingParty(): Attribute
    {
        return Attribute::get(
            fn () => $this->incidents->count() > 0,
        );
    }

    protected function isDonor(): Attribute
    {
        return Attribute::get(
            fn () => $this->donations->count() > 0,
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
        static::saving(function (Person $person) {
            $administrativeDivision = app(AdministrativeDivision::class);

            if ($person->wasChanged('phone') || ! $person->exists) {
                $person->phone_normalized = $administrativeDivision->phoneNumber($person->phone, format: PhoneFormat::NORMALIZED);
                $person->phone_e164 = $administrativeDivision->phoneNumber($person->phone, format: PhoneFormat::E164);
                $person->phone_national = $administrativeDivision->phoneNumber($person->phone, format: PhoneFormat::NATIONAL);
            }

            if ($person->wasChanged('alternate_phone') || ! $person->exists) {
                $person->alternate_phone_normalized = $administrativeDivision->phoneNumber($person->alternate_phone, format: PhoneFormat::NORMALIZED);
                $person->alternate_phone_e164 = $administrativeDivision->phoneNumber($person->alternate_phone, format: PhoneFormat::E164);
                $person->alternate_phone_national = $administrativeDivision->phoneNumber($person->alternate_phone, format: PhoneFormat::NATIONAL);
            }
        });
    }
}
