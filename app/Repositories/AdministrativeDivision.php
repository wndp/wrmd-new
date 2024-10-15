<?php

namespace App\Repositories;

use CommerceGuys\Addressing\Address;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Sokil\IsoCodes\Database\Subdivisions\Subdivision;
use Sokil\IsoCodes\IsoCodesFactory;
use Sokil\IsoCodes\TranslationDriver\SymfonyTranslationDriver;

class AdministrativeDivision
{
    public $isoCodes;

    public function __construct(private string $locale, private string $alpha2CountryCode)
    {
        $driver = new SymfonyTranslationDriver();
        $driver->setLocale($locale);

        $this->isoCodes = new IsoCodesFactory(null, $driver);
    }

    /**
     * Get an array of the words countries.
     *
     * @return array
     */
    public function countries(): array
    {
        return Arr::mapWithKeys($this->isoCodes->getCountries()->toArray(),  fn ($country) => [
            $country->getAlpha2() => $country->getLocalName()
        ]);
    }

    /**
     * Get an array of a countries subdivisions.
     *
     * @param  string|null $alpha2CountryCode
     * @return array
     */
    public function countrySubdivisions(string $alpha2CountryCode = null): array
    {
        $subdivisions = $this->isoCodes->getSubdivisions()->getAllByCountryCode($alpha2CountryCode ?? $this->alpha2CountryCode);

        return Arr::mapWithKeys($subdivisions,  fn ($country) => [
            $country->getCode() => $country->getLocalName()
        ]);
    }

    /**
     * Get a slash separated list of a countries subdivision names.
     *
     * @param  string|null $alpha2CountryCode
     * @return string
     */
    public function countrySubdivisionType(string $alpha2CountryCode = null): string
    {
        $subdivisions = $this->isoCodes->getSubdivisions()->getAllByCountryCode($alpha2CountryCode ?? $this->alpha2CountryCode);

        if (empty($subdivisions)) {
            return '--';
        }

        return Collection::make($subdivisions)
            ->map(fn ($subdivision) => $subdivision->getType())
            ->unique()
            ->implode(' / ');

    }

    /**
     * Get a countries timezones.
     *
     * @param  string|null $alpha2CountryCode
     * @return array
     */
    public function countryTimeZones(string $alpha2CountryCode = null): array
    {
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $alpha2CountryCode ?? $this->alpha2CountryCode);

        return Collection::make($timezones)->mapWithKeys(fn ($timezone) => [
                $timezone => Str::headline(
                    Str::of($timezone)->explode('/')->last()
                )
            ])
            ->sort()
            ->toArray();
    }

    /**
     * Get a countries ISO 4217 currency letter code. Ex: "USD"
     *
     * @param  string|null $alpha2CountryCode
     * @return string
     */
    public function countryCurrencyCode(string $alpha2CountryCode = null): string
    {
        $country = $this->isoCodes->getCountries()->getByAlpha2($alpha2CountryCode ?? $this->alpha2CountryCode);

        if (is_null($country)) return '';

        $currency = $this->isoCodes->getCurrencies()->getByNumericCode($country->getNumericCode());

        return $currency?->getLetterCode();
    }

    /**
     * Format an address for inline presentation.
     *
     * @param  string|null $alpha2CountryCode
     * @param  string|null $subdivision
     * @param  string|null $city
     * @param  string|null $address
     * @param  string|null $postalCode
     * @param  string|null $organization
     * @return string
     */
    public function inlineAddress(
        string $alpha2CountryCode = null,
        string $subdivision = null,
        string $city = null,
        string $addressLine1 = null,
        string $postalCode = null,
        string $organization = null
    ): string {
        $formatter = new DefaultFormatter(
            new AddressFormatRepository(),
            new CountryRepository(),
            new SubdivisionRepository()
        );

        $address = (new Address())
            ->withLocale($this->locale)
            ->withCountryCode($alpha2CountryCode ?? $this->alpha2CountryCode)
            ->withAdministrativeArea($subdivision ?: '')
            ->withLocality($city ?: '')
            ->withAddressLine1($addressLine1 ?: '')
            ->withPostalCode($postalCode ?: '')
            ->withOrganization($organization ?: '');

        return $formatter->format($address, [
            'html' => false
        ]);
    }
}
