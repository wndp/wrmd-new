<?php

namespace App\Options;

use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LocaleOptions extends Options
{
    public static $languages = [
        'en' => '(en) English',
        'es' => '(es) Español',
        'fr' => '(fr) Français',
    ];

    public function toArray(): array
    {
        $countryRepository = new CountryRepository();
        $subdivisionRepository = new SubdivisionRepository();

        $countries = $countryRepository->getList(app()->getLocale());
        $timezones = static::formatTimezones($countryRepository->get('US')->getTimezones());
        $subdivisions = $subdivisionRepository->getList(['US'], app()->getLocale());

        return [
            'countryOptions' => static::arrayToSelectable($countries),
            'subdivisionOptions' => static::arrayToSelectable($subdivisions),
            'timezoneOptions' => static::arrayToSelectable($timezones),
            'languageOptions' => static::arrayToSelectable(static::$languages),
        ];
    }

    public static function formatTimezones($timezones)
    {
        return Collection::make($timezones)->map(fn ($timezone) => Str::headline(
            Str::of($timezone)->explode('/')->last()
        ))
        ->sort()
        ->toArray();
    }
}
