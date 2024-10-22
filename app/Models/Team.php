<?php

namespace App\Models;

use App\Concerns\HasSubAccounts;
use App\Enums\AccountStatus;
use App\Repositories\AdministrativeDivision;
use App\Repositories\SettingsStore;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
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
    use Billable;
    use HasFactory;
    use HasProfilePhoto;
    use HasSubAccounts;
    use LogsActivity;

    protected $fillable = [
        'name',
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
        'phone_number',
        'website',
        'federal_permit_number',
        'subdivision_permit_number',
        'profile_photo_path',
        'notes',
    ];

    protected $appends = [
        'profile_photo_url',
        'formatted_inline_address',
    ];

    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'status' => AccountStatus::class
        ];
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(TeamExtension::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function settingsStore(): SettingsStore
    {
        return new SettingsStore($this);
    }

    protected function formattedInlineAddress(): Attribute
    {
        return Attribute::get(fn () =>
            app(AdministrativeDivision::class)->inlineAddress(
                alpha2CountryCode: $this->country,
                subdivision: $this->subdivision,
                city: $this->city,
                addressLine1: $this->address,
                postalCode: $this->postal_code
            )
        );
    }

    public function phoneNumber(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => app(AdministrativeDivision::class)->phoneNumber($value),
            set: fn ($value) => preg_replace('/[^0-9]/', '', $value)
        );
    }

    /**
     * Get the name that should be associated with the Paddle customer.
     */
    public function paddleName(): string|null
    {
        return $this->name;
    }

    /**
     * Get the email address that should be associated with the Paddle customer.
     */
    public function paddleEmail(): string|null
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
}
