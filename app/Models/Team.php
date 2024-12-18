<?php

namespace App\Models;

use App\Concerns\HasSubAccounts;
use App\Enums\AccountStatus;
use App\Enums\PhoneFormat;
use App\Repositories\AdministrativeDivision;
use App\Repositories\SettingsStore;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\Team as JetstreamTeam;
use Spark\Billable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Team extends JetstreamTeam
{
    //use Billable;
    use HasFactory;
    use HasProfilePhoto;
    use HasSubAccounts;
    use LogsActivity;
    use Notifiable;

    protected $fillable = [
        'name',
        'status',
        'personal_team',
        'is_master_account',
        'contact_name',
        'contact_email',
        'country',
        'address',
        'city',
        'subdivision',
        'postal_code',
        'coordinates',
        'phone',
        'phone_e164',
        'phone_normalized',
        'phone_national',
        'website',
        'federal_permit_number',
        'subdivision_permit_number',
        'profile_photo_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_master_account' => 'boolean',
            'personal_team' => 'boolean',
            'status' => AccountStatus::class,
            'phone' => 'string',
            'phone_normalized' => 'string',
            'phone_e164' => 'string',
            'phone_national' => 'string',
        ];
    }

    protected $appends = [
        'profile_photo_url',
        'formatted_inline_address',
    ];

    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class);
    }

    public function expenseCategories(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(TeamExtension::class);
    }

    public function formulas(): HasMany
    {
        return $this->hasMany(Formula::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function veterinarians(): HasMany
    {
        return $this->hasMany(Veterinarian::class);
    }

    public function settingsStore(): SettingsStore
    {
        return new SettingsStore($this);
    }

    protected function formattedInlineAddress(): Attribute
    {
        return Attribute::get(
            fn () => app(AdministrativeDivision::class)->inlineAddress(
                alpha2CountryCode: $this->country,
                subdivision: $this->subdivision,
                city: $this->city,
                addressLine1: $this->address,
                postalCode: $this->postal_code
            )
        );
    }

    // public function phoneNumber(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => app(AdministrativeDivision::class)->phoneNumber($value),
    //         set: fn ($value) => preg_replace('/[^0-9]/', '', $value)
    //     );
    // }

    /**
     * Get the name that should be associated with the Paddle customer.
     */
    public function paddleName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the email address that should be associated with the Paddle customer.
     */
    public function paddleEmail(): ?string
    {
        return $this->contact_email;
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
        static::saving(function (Team $team) {
            $administrativeDivision = app(AdministrativeDivision::class);

            if ($team->wasChanged('phone') || !$team->exists) {
                $team->phone_normalized = $administrativeDivision->phoneNumber($team->phone, format: PhoneFormat::NORMALIZED);
                $team->phone_e164 = $administrativeDivision->phoneNumber($team->phone, format: PhoneFormat::E164);
                $team->phone_national = $administrativeDivision->phoneNumber($team->phone, format: PhoneFormat::NATIONAL);
            }
        });
    }
}
