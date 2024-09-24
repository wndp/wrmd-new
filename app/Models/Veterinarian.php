<?php

namespace App\Models;

use App\Concerns\ValidatesOwnership;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Veterinarian extends Model
{
    use HasFactory;
    use ValidatesOwnership;
    use HasVersion7Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'license',
        'business_name',
        'address',
        'city',
        'subdivision',
        'postal_code',
        'phone',
        'email',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Present the veterinarians full address.
     */
    public function getFullAddressAttribute(): string
    {
        // $driver = new \Sokil\IsoCodes\TranslationDriver\SymfonyTranslationDriver();
        // $isoCodes = new \Sokil\IsoCodes\IsoCodesFactory(null, $driver);
        // $subDivisions = $isoCodes->getSubdivisions();


        // $subDivision = $subDivisions->getByCode(
        //     \App\Support\Wrmd::iso3166(dd($this->team->country, $this->subdivision))
        // );

        // dd($subDivision->getName()); // Respublika Krym

        //$subDivision->getLocalName(); // Автономна Республіка Крим

        // get subdivision type
        //$subDivision->getType(); // 'Autonomous republic'

        $address = new \CommerceGuys\Addressing\Address(
            countryCode: $this->team->country,
            organization: $this->business_name ?? '',
            administrativeArea: $this->subdivision ?? '', // this should be the actual name not the abreviation
            locality: $this->city ?? '',
            postalCode: $this->postal_code ?? '',
            addressLine1: $this->address ?? '',
        );

        $addressFormatRepository = new \CommerceGuys\Addressing\AddressFormat\AddressFormatRepository();
        $countryRepository = new \CommerceGuys\Addressing\Country\CountryRepository();
        $subdivisionRepository = new \CommerceGuys\Addressing\Subdivision\SubdivisionRepository();
        $formatter = new \CommerceGuys\Addressing\Formatter\DefaultFormatter(
            $addressFormatRepository,
            $countryRepository,
            $subdivisionRepository
        );

        return $formatter->format($address, [
            'html_tag' => 'address',
            'locale' => app()->getLocale()
        ]);
    }
}
