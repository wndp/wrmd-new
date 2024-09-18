<?php

namespace App\Http\Controllers;

use App\Options\LocaleOptions;
use App\Options\Options;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Illuminate\Http\JsonResponse;

class LocaleController extends Controller
{
    /**
     * Show the details for the request country.
     */
    public function __invoke(string $country): JsonResponse
    {
        $countryRepository = new CountryRepository();
        $subdivisionRepository = new SubdivisionRepository();

        $timezones = LocaleOptions::formatTimezones($countryRepository->get($country)->getTimezones());
        $subdivisions = $subdivisionRepository->getAll([$country]);

        return response()->json([
            'subdivisions' => Options::arrayToSelectable($subdivisions),
            'timezones' => Options::arrayToSelectable($timezones),
        ]);

        //$locale = locale()->get($country);

        // return response()->json(array_merge($locale->jsonSerialize(), [
        //     'subdivisions' => Options::arrayToSelectable(array_keys($locale::$subdivisions)),
        //     'timezones' => Options::arrayToSelectable($locale::timezones()),
        // ]));
    }
}
