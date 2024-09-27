<?php

namespace App\Models;

use App\Concerns\HasSubAccounts;
use App\Enums\AccountStatus;
use App\Repositories\SettingsStore;
use CommerceGuys\Addressing\Address;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\Team as JetstreamTeam;
use Spark\Billable;

class Team extends JetstreamTeam
{
    use Billable;
    use HasFactory;
    use HasProfilePhoto;
    use HasSubAccounts;

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
        return Attribute::get(function () {
            $formatter = new DefaultFormatter(
                new AddressFormatRepository(),
                new CountryRepository(),
                new SubdivisionRepository()
            );

            $address = (new Address())
                ->withCountryCode($this->country)
                ->withAdministrativeArea($this->subdivision ?: '')
                ->withLocality($this->city ?: '')
                ->withAddressLine1($this->address ?: '')
                ->withPostalCode($this->postal_code ?: '');

            return $formatter->format($address, [
                'html' => false
            ]);
        });
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
}
