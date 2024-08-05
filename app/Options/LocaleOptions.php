<?php

namespace App\Options;

use Illuminate\Support\Arr;

class LocaleOptions extends Options
{
    public static $languages = [
        'en' => '(en) English',
        'es' => '(es) Español',
        'fr' => '(fr) Français',
    ];

    public function toArray(): array
    {
        $driver = new \Sokil\IsoCodes\TranslationDriver\SymfonyTranslationDriver();
        $isoCodes = new \Sokil\IsoCodes\IsoCodesFactory(null, $driver);

        //$countries = $isoCodes->getCountries();//->getByAlpha2('US');
        //dd($countries);

        $countries = Arr::mapWithKeys(
            $isoCodes->getCountries()->toArray(),
            fn ($country) => [$country->getAlpha2() => $country->getLocalName()]
        );

        $subdivisions = Arr::map(
            $isoCodes->getSubdivisions()->getAllByCountryCode('US'),
            fn ($subdivision) => $subdivision->getLocalName()
        );

        return [
            'countries' => static::arrayToSelectable($countries),
            'subdivisions' => static::arrayToSelectable($subdivisions),
            'timezones' => static::arrayToSelectable($this->timezones('US')),
            'languages' => static::arrayToSelectable(static::$languages),
        ];
    }

    public static function timezones($countryCode)
    {
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $countryCode);

        return array_reduce($timezones, function ($carry, $timezone) {
            $carry[$timezone] = \Illuminate\Support\Str::headline(
                \Illuminate\Support\Str::of($timezone)->explode('/')->last()
            );

            return $carry;
        }, []);
    }
}
