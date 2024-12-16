<?php

namespace App\Options;

use App\Repositories\AdministrativeDivision;

class LocaleOptions extends Options
{
    public static $languages = [
        'en' => '(en) English',
        'es' => '(es) Español',
        'fr' => '(fr) Français',
    ];

    public function toArray(): array
    {
        $adminDivision = app(AdministrativeDivision::class);

        $countries = $adminDivision->countries();
        $subdivisions = $adminDivision->countrySubdivisions();
        $timezones = $adminDivision->countryTimeZones();

        return [
            'countryOptions' => static::arrayToSelectable($countries),
            'subdivisionOptions' => static::arrayToSelectable($subdivisions),
            'timezoneOptions' => static::arrayToSelectable($timezones),
            'languageOptions' => static::arrayToSelectable(static::$languages),
        ];
    }
}
