<?php

namespace App\Http\Controllers;

use App\Options\LocaleOptions;
use App\Options\Options;
use App\Repositories\AdministrativeDivision;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    /**
     * Show the details for the requested country.
     */
    public function __invoke(string $country): JsonResponse
    {
        $adminDivision = app(AdministrativeDivision::class);
        $subdivisions = $adminDivision->countrySubdivisions($country);
        $timezones = $adminDivision->countryTimeZones($country);

        return response()->json([
            'subdivisionType' => $adminDivision->countrySubdivisionType($country),
            'subdivisions' => Options::arrayToSelectable($subdivisions),
            'timezones' => Options::arrayToSelectable($timezones),
        ]);
    }
}
