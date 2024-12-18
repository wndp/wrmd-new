<?php

namespace App\Repositories;

use App\Enums\PhoneFormat;
use CommerceGuys\Addressing\Address;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\Exceptions\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;
use Sokil\IsoCodes\Database\Subdivisions\Subdivision;
use Sokil\IsoCodes\IsoCodesFactory;
use Sokil\IsoCodes\TranslationDriver\SymfonyTranslationDriver;

class AdministrativeDivision
{
    public $isoCodes;

    public function __construct(private string $locale, private string $alpha2CountryCode)
    {
        $driver = new SymfonyTranslationDriver;
        $driver->setLocale($locale);

        $this->isoCodes = new IsoCodesFactory(null, $driver);
    }

    /**
     * Get an array of the worlds countries.
     */
    public function countries(): array
    {
        return Arr::mapWithKeys($this->isoCodes->getCountries()->toArray(), fn ($country) => [
            $country->getAlpha2() => $country->getLocalName(),
        ]);
    }

    /**
     * Get a country's name.
     */
    public function countryName(?string $alpha2CountryCode = null): string
    {
        return $this->isoCodes->getCountries()->getByAlpha2($alpha2CountryCode ?? $this->alpha2CountryCode)->getLocalName();
    }

    /**
     * Get an array of a country's subdivisions.
     */
    public function countrySubdivisions(?string $alpha2CountryCode = null): array
    {
        $subdivisions = $this->isoCodes->getSubdivisions()->getAllByCountryCode($alpha2CountryCode ?? $this->alpha2CountryCode);

        return Arr::mapWithKeys($subdivisions, fn ($country) => [
            $country->getCode() => $country->getLocalName(),
        ]);
    }

    /**
     * Get a slash separated list of a countries subdivision names.
     */
    public function countrySubdivisionType(?string $alpha2CountryCode = null): string
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
     */
    public function countryTimeZones(?string $alpha2CountryCode = null): array
    {
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $alpha2CountryCode ?? $this->alpha2CountryCode);

        return Collection::make($timezones)->mapWithKeys(fn ($timezone) => [
            $timezone => Str::headline(
                Str::of($timezone)->explode('/')->last()
            ),
        ])
            ->sort()
            ->toArray();
    }

    /**
     * Get a countries ISO 4217 currency letter code. Ex: "USD"
     */
    public function countryCurrencyCode(?string $alpha2CountryCode = null): string
    {
        $country = $this->isoCodes->getCountries()->getByAlpha2($alpha2CountryCode ?? $this->alpha2CountryCode);

        if (is_null($country)) {
            return '';
        }

        $currency = $this->isoCodes->getCurrencies()->getByNumericCode($country->getNumericCode());

        return $currency?->getLetterCode();
    }

    /**
     * Format an address for inline presentation.
     *
     * @param  string|null  $address
     */
    public function inlineAddress(
        ?string $alpha2CountryCode = null,
        ?string $subdivision = null,
        ?string $city = null,
        ?string $addressLine1 = null,
        ?string $postalCode = null,
        ?string $organization = null,
        ?string $name = null
    ): string {
        return $this->formatAddress(
            true,
            $alpha2CountryCode,
            $subdivision,
            $city,
            $addressLine1,
            $postalCode,
            $organization,
            $name
        );
    }

    /**
     * Format an address for inline presentation.
     *
     * @param  string|null  $address
     */
    public function blockAddress(
        ?string $alpha2CountryCode = null,
        ?string $subdivision = null,
        ?string $city = null,
        ?string $addressLine1 = null,
        ?string $postalCode = null,
        ?string $organization = null,
        ?string $name = null
    ): string {
        return $this->formatAddress(
            false,
            $alpha2CountryCode,
            $subdivision,
            $city,
            $addressLine1,
            $postalCode,
            $organization,
            $name
        );
    }

    /**
     * Create an address using the provided parts.
     *
     * @return Address
     */
    private function formatAddress(
        bool $inline = false,
        ?string $alpha2CountryCode = null,
        ?string $subdivision = null,
        ?string $city = null,
        ?string $addressLine1 = null,
        ?string $postalCode = null,
        ?string $organization = null,
        ?string $name = null
    ): string {
        $address = (new Address)
            ->withLocale($this->locale)
            ->withCountryCode($alpha2CountryCode ?? $this->alpha2CountryCode)
            ->withAdministrativeArea($subdivision ?: '')
            ->withLocality($city ?: '')
            ->withAddressLine1($addressLine1 ?: '')
            ->withPostalCode($postalCode ?: '')
            ->withOrganization($organization ?: '')
            ->withGivenName($name ?: '');

        $formatter = new DefaultFormatter(
            new AddressFormatRepository,
            new CountryRepository,
            new SubdivisionRepository
        );

        return str_replace("\n", ' ', $formatter->format($address, [
            'html' => ! $inline,
        ]));
    }

    /**
     * Format a phone number according to its nationality.
     *
     * @param  string  $phoneNumber
     * @return string
     */
    public function phoneNumber($phoneNumber, ?string $alpha2CountryCode = null, ?PhoneFormat $format = null)
    {
        try {
            $phone = new PhoneNumber($phoneNumber, $alpha2CountryCode ?? $this->alpha2CountryCode);

            return match ($format) {
                PhoneFormat::E164 => $phone->formatE164(),
                PhoneFormat::NATIONAL => $phone->formatNational(),
                PhoneFormat::NORMALIZED => preg_replace('/[^0-9]/', '', $phoneNumber),
                default => preg_replace('/[^0-9]/', '', $phoneNumber),
            };
        } catch (NumberParseException $e) {
            return preg_replace('/[^0-9]/', '', $phoneNumber);
        }

        //$phone->formatForCountry($alpha2CountryCode ?? $this->alpha2CountryCode);

        // $phoneUtil = PhoneNumberUtil::getInstance();

        // try {
        //     $numberProto = $phoneUtil->parse($phoneNumber, $alpha2CountryCode ?? $this->alpha2CountryCode);
        // } catch (NumberParseException $e) {
        //     return $phoneNumber;
        // }

        // return $phoneUtil->isValidNumber($numberProto)
        //     ? $phoneUtil->format($numberProto, PhoneNumberFormat::NATIONAL)
        //     : $phoneNumber;
    }
}
