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

class Team extends JetstreamTeam
{
    use HasFactory;
    use HasProfilePhoto;
    use HasSubAccounts;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'status' => AccountStatus::class
        ];
    }

    /**
     * An account has many extensions.
     */
    public function extensions(): BelongsToMany
    {
        return $this->belongsToMany(Extension::class, 'team_extension', 'team_id', 'extension_id')->withTimestamps();
    }

    /**
     * Get the accounts settings.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    /**
     * Return the accounts SettingsStore.
     */
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
                ->withAdministrativeArea($this->subdivision)
                ->withLocality($this->city)
                ->withAddressLine1($this->address)
                ->withPostalCode($this->postal_code);

            return $formatter->format($address, [
                'html' => false
            ]);
        });
    }
}
